<?php
namespace Ipsum\Media\Controllers;

use View;
use Input;
use Redirect;
use Session;
use Str;
use File;
use Config;
use Response;
use Validator;
use Request;
use Liste;
use HTML;
use Croppa;
use Ipsum\Media\Models\Media;
use Ipsum\Media\Models\MediaPublication;

class AdminController extends \Ipsum\Admin\Controllers\BaseController {

    public $title = 'Gestion des médias';
    public $rubrique = 'media';
    public $menu = 'media';
    public static $zone = 'media';

    public function index()
    {
        $requete = Media::select('*');
        $liste = Liste::setRequete($requete);
        $filtres = array(
            array(
                'nom' => 'mot',
                'operateur' => 'like',
                'colonnes' => array (
                    'media.titre',
                ),
            ),
            array(
                'nom' => 'repertoire',
                'colonnes' => 'media.repertoire',
            ),
            array(
                'nom' => 'type',
                'colonnes' => 'media.type',
            ),
        );
        Liste::setFiltres($filtres);
        $tris = array(
            array(
                'nom' => 'date',
                'ordre' => 'desc',
                'colonne' => 'created_at',
                'actif' => true,
            ),
            array(
                'nom' => 'titre',
            ),
            array(
                'nom' => 'type',
            ),
            array(
                'nom' => 'repertoire',
            ),
        );
        Liste::setTris($tris);

        $medias = Liste::rechercherLignes();

        $types[''] = '----- Type ------';
        foreach (Config::get('IpsumMedia::types') as $type) {
            $types[$type['type']] = $type['type'];
        }
        $repertoires[''] = '----- Répertoire ------';
        foreach (Config::get('IpsumMedia::repertoires') as $repertoire) {
            $repertoires[$repertoire] = $repertoire;
        }

        $this->layout->content = View::make('IpsumMedia::admin.index', compact('medias', 'types', 'repertoires'));
    }


    public function upload()
    {
        $succes = $error = $messages = null;
        $medias = array();

        $files = Input::file('medias');
        if (!is_array($files)) {
            $files[] = $files;
        }

        $mimes = array();
        foreach (Config::get('IpsumMedia::types') as $type) {
            $mimes = array_merge($mimes, $type['mimes']);
        }
        $mimesAccepted = implode(',', $mimes);

        $repertoire = (Input::has('repertoire') and in_array(Input::get('repertoire'), Config::get('IpsumMedia::repertoires'))) ? Input::get('repertoire').'/' : '';

        foreach ($files as $file) {

            $rules = array($file->getClientOriginalName()  => 'mimes:'.$mimesAccepted.'|max:10000');
            $datas = array($file->getClientOriginalName() => $file);
            $validation = Validator::make($datas, $rules);
            if ($validation->passes()) {
                try {
                    $extension = strtolower(File::extension($file->getClientOriginalName()));
                    $basename = basename($file->getClientOriginalName(),'.'.$extension);
                    $titre = str_replace(array('-', '_'), ' ', $basename);
                    $basename = Str::slug($basename);
                    $filename = $basename.'.'.$extension;

                    // Renomme si fichier existe déja
                    $count = 1;
                    while (File::exists(Config::get('IpsumMedia::path').$repertoire.$filename)) {
                        $filename = $basename.'('.$count++.').'.$extension;
                    }

                    // Récupèration du type de fichier
                    $type = null;
                    foreach (Config::get('IpsumMedia::types') as $value) {
                        if (in_array($extension, $value['mimes'])) {
                            $type = $value['type'];
                            break;
                        }
                    }

                    // Enregistrement du fichier
                    $file->move(Config::get('IpsumMedia::path').$repertoire, $filename);

                    // Enregistrement ne bdd
                    $media = new Media;
                    $media->titre = $titre;
                    $media->fichier = $filename;
                    $media->type = $type;
                    $media->repertoire = str_replace('/', '', $repertoire);
                    $media->save();

                    // Enregsitrement de la publication associé
                    if (Input::has('publication_id') and Input::has('publication_type')) {
                        $mediaPublication = new MediaPublication;
                        $mediaPublication->publication_id = Input::get('publication_id');
                        $mediaPublication->publication_type = Input::get('publication_type');
                        $media->publications()->save($mediaPublication);
                    } elseif (Input::has('publication_type')) {
                        // Cas des médias qui ne sont pas encore associé à une pulbication
                        // Cas de l'upload avant la création de la publication
                        $mediaPublications[] = array(
                            'publication_type' => Input::get('publication_type'),
                            'media_id' => $media->id
                        );
                        if (Session::has('media.publications')) {
                            $mediaPublications = array_merge(Session::get('media.publications'), $mediaPublications);
                        }
                        Session::put('media.publications', $mediaPublications);
                    }

                    // Eléments nécessaire pour affichage ajax
                    if ($media->isImage()) {
                        $media->image = Croppa::url('/'.$media->path, 150, 150);
                    }
                    $media->url = asset($media->path);
                    $media->date = $media->created_at->format('d/m/Y');
                    $media->icone = $media->icone;
                    $medias[] = $media;

                    $succes[] = "Le média ".$file->getClientOriginalName()." a bien été téléchargé";
                } catch (\RuntimeException $e) {
                   $error[] = "Votre média ".$file->getClientOriginalName()." est trop lourd.";
                } catch (\Exception $e) {
                   $error[] =  "Impossible de télécharger le média ".$file->getClientOriginalName();
                }
             } else {
                $messages = $validation->messages();
                foreach ($messages->all() as $message) {
                    $error[] =  $message;
                }
             }
        }

        Session::flash('success', $succes);
        Session::flash('error', $error);

        if (Request::ajax()) {
            $notifications = HTML::notifications();
            Session::forget('success');
            Session::forget('error');
            return Response::json(
                array(
                    'errors' => ($error === null ? 0 : $error),
                    'medias' => $medias,
                    'notifications' => $notifications
                )
            );
        }
        return Redirect::back();
    }

    public function destroy($id)
    {

        $media = Media::findOrFail($id);

        $repertoire = !empty($media->repertoire) ? $media->repertoire.'/' : '';

        File::deleteAll(Config::get('IpsumMedia::path').$repertoire.$media->fichier);
        Croppa::delete(Config::get('IpsumMedia::path').$repertoire.$media->fichier); // TODO ne fonctionne pas

        $media->publications()->delete();
        $media->delete();

        Session::flash('warning', "Le media a bien été supprimé");

        return Redirect::back();
    }

    public function detach($id, $publication_id)
    {

        $media = Media::findOrFail($id);

        $media->publications()->where('publication_id', $publication_id)->delete();

        Session::flash('warning', "Le média a bien été détaché");

        return Redirect::back();
    }

}
