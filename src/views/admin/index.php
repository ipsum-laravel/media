
<div>
    <h2>Ajouter des fichiers</h2>
    <div id="fileupload-message"></div>
    <?= Form::open(
        array(
            'route' => array('admin.media.upload'),
            'method' => 'PUT',
            'files' => true
        )
    ); ?>
        <div id="fileupload">
            <input name="medias[]" id="files" type="file" multiple="multiple">
            <input name="submit" type="submit" value="Télécharger">
        </div>
    <?= Form::close() ?>
    <script id="tmpl-media" type="x-tmpl-mustache">
        {{#medias}}
            <tr class="pair">
                <td>{{{date}}}</td>
                <td>{{{titre}}}</td>
                <td>{{{repertoire}}}</td>
                <td>{{{type}}}</td>
                <td class="center">
                    <a href="{{{url}}}">
                        {{#image}}
                        <img src="{{{image}}}" alt="" />
                        {{/image}}
                        {{^image}}
                        <img src="<?= asset('packages/ipsum/media/img') ?>/{{{icone}}}" alt="" />
                        {{/image}}
                    </a>
                </td>
                {{=|| ||=}}
                <td class="center">
                    <?= Form::open(array('method' => 'DELETE', 'route' => array('admin.media.destroy', '||id||'))) ?>
                        <div>
                            <input type="image" src="<?= asset('packages/ipsum/admin/img/supprimer.png') ?>" value="Supprimer" class="supprimer" data-message="||titre||">
                        </div>
                    <?= Form::close() ?>
                </td>
                ||={{ }}=||
           </tr>
       {{/medias}}
    </script>

    <h2 style="margin-top: 20px;">Liste des médias (<?= Liste::count() ?>)</h2>
    <?= Liste::pagination() ?>
    <form method="get" id="recherche" action="">
        <div>
            <?= Liste::inputsHidden() ?>
            <input type="text" name="mot" id="mot" value="<?= Liste::getFiltreValeur('mot') ?>" />
            <?= Form::select('repertoire', $repertoires, Liste::getFiltreValeur('repertoire')) ?>
            <?= Form::select('type', $types, Liste::getFiltreValeur('type')) ?>
            <input type="submit" name="submit" value="Chercher" />
        </div>
    </form>
    <table class="liste" style="width: 100%;">
        <thead>
            <tr>
                <th><?= Liste::lienTri('Date', 'date') ?></th>
                <th><?= Liste::lienTri('Titre', 'titre') ?></th>
                <th><?= Liste::lienTri('Répertoire', 'repertoire') ?></th>
                <th><?= Liste::lienTri('Type', 'type') ?></th>
                <th>Média</th>
                <th>Supp.</th>
            </tr>
        </thead>
        <tbody id="medias">
            <?php $i=0; foreach ($medias as $media): ?>
                <tr class="<?= (($i %2 ) == 0 ? "pair" : "impair") ?>">
                    <td><?= e($media->created_at->format('d/m/Y')) ?></td>
                    <td><?= e($media->titre) ?></td>
                    <td><?= e($media->repertoire) ?></td>
                    <td><?= e($media->type) ?></td>
                    <td class="center">
                        <a href="<?= e($media->url) ?>">
                            <?php if ($media->image) : ?>
                            <img src="<?= Croppa::url('/'.$media->image, 150, 150) ?>" alt="" />
                            <?php else : ?>
                            <img src="<?= asset('packages/ipsum/media/img/'.$media->icone) ?>" alt="<?= e($media->type) ?>" />
                            <?php endif ?>
                        </a>
                    </td>
                    <td class="center">
                        <?= Form::open(array('method' => 'DELETE', 'route' => array('admin.media.destroy', $media->id))) ?>
                            <div>
                                <input type="image" src="<?= asset('packages/ipsum/admin/img/supprimer.png') ?>" value="Supprimer" class="supprimer" data-message="<?= e($media->titre) ?>">
                            </div>
                        <?= Form::close() ?>
                    </td>
               </tr>
            <?php $i++; endforeach; ?>
        </tbody>
    </table>
    <?= Liste::pagination() ?>
</div>

<!--  fileupload -->
<script src="<?= asset('packages/ipsum/admin/js/jquery.ui.widget.js') ?>"></script>
<script src="<?= asset('packages/ipsum/media/js/jquery.knob.js') ?>"></script>
<script src="<?= asset('packages/ipsum/media/js/jquery.iframe-transport.js') ?>" ></script>
<script src="<?= asset('packages/ipsum/media/js/jquery.fileupload.js') ?>" ></script>
<script src="<?= asset('packages/ipsum/admin/js/mustache.js') ?>" ></script>
<script src="<?= asset('packages/ipsum/media/js/jquery.formFileupload.js') ?>" ></script>
<script>
    $(function() {
        $("#fileupload").formFileupload({
            "afterDone" : function (e, data) {
                var template = $("#tmpl-media").html();
                Mustache.parse(template);   // optional, speeds up future uses
                var rendered = Mustache.render(template, data.result);
                $("#medias").prepend(rendered);
            }
        });
    });
</script>

<style>
    .fileupload {
        padding: 20px;
        background-color: #fafafa;
        border: dashed 4px #9f9f9f;

        text-align: center;
    }
    .fileupload-hover {
        border-color: #dedede;
    }
    .fileupload-error {
        border: dashed 4px red;
    }
    .fileupload input{
        display:none;
    }
</style>

