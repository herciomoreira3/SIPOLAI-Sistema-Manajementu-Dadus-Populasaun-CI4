<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 16px;
        border: none;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
</style>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-edit mr-2"></i> Konfigura Formatu Deklarasaun: <?= esc($tipu['naran_tipu_pedidu']) ?></h3>
            </div>
            <form action="<?= base_url('admin/formatu-deklarasaun/' . $tipu['id_tipu_pedidu'] . '/update') ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>

                <!-- Signature Upload Panel -->
                <div class="card bg-light border-0 p-3 mb-4 rounded-lg">
                    <h5 class="font-weight-bold text-secondary mb-3"><i class="fas fa-signature mr-2"></i> Upload & Substitui Tanda Tangan</h5>
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <label class="font-weight-bold text-muted">Tanda Tangan Chefe Suco</label>
                            <div class="input-group mb-2">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="upload-chefe" accept="image/*">
                                    <label class="custom-file-label" for="upload-chefe" id="label-chefe">Hili fail imajen...</label>
                                </div>
                            </div>
                            <small class="text-muted d-block mb-2">Sei substitui <code>https://your-link-tanda-tangan-chefe.jpg</code> ka insert foun.</small>
                            <button type="button" class="btn btn-sm btn-info btn-block btn-rounded" id="btn-apply-chefe"><i class="fas fa-magic mr-1"></i> Aplika / Substitui</button>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold text-muted">Tanda Tangan Visto (Posto Adm)</label>
                            <div class="input-group mb-2">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="upload-visto" accept="image/*">
                                    <label class="custom-file-label" for="upload-visto" id="label-visto">Hili fail imajen...</label>
                                </div>
                            </div>
                            <small class="text-muted d-block mb-2">Sei substitui <code>https://your-link-tanda-tangan-visto.jpg</code> ka insert foun.</small>
                            <button type="button" class="btn btn-sm btn-info btn-block btn-rounded" id="btn-apply-visto"><i class="fas fa-magic mr-1"></i> Aplika / Substitui</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="template_formatu" class="font-weight-bold text-muted">Template Formatu (HTML/Text)</label>
                    <textarea name="template_formatu" id="template_formatu" class="form-control" rows="15" placeholder="Konfigura formatu deklarasaun, uza variable sira hanesan [NARAN_KOMPLETU], [DATA_MORIS], [NIK], [ALDEIA]..."><?= esc($tipu['template_formatu']) ?></textarea>
                    <small class="text-muted d-block mt-2">
                        Variable sira ne'ebé bele uza iha template laran: <code>[NARAN_KOMPLETU]</code>, <code>[NIK]</code>, <code>[ALDEIA]</code>, <code>[DATA_MORIS]</code>.
                    </small>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= route_to('formatu-deklarasaun') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded">Aktualiza Formatu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Include summernote CDN for premium WYSIWYG editor -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#template_formatu').summernote({
            height: 500,
            placeholder: 'Prepara formatu karta iha ne\'e...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'strikethrough', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            popover: {
                image: [
                    ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ],
                link: [
                    ['link', ['linkDialogShow', 'unlink']]
                ],
                table: [
                    ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                    ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
                ]
            },
            callbacks: {
                onImageUpload: function(files) {
                    for (let i = 0; i < files.length; i++) {
                        uploadImage(files[i]);
                    }
                }
            }
        });

        // Helper to encode uploaded images to base64 for seamless template storage
        function uploadImage(file) {
            var reader = new FileReader();
            reader.onloadend = function() {
                var imgNode = $('<img>').attr('src', reader.result).css({
                    'max-width': '100%',
                    'height': 'auto',
                    'margin': '10px'
                });
                $('#template_formatu').summernote('insertNode', imgNode[0]);
            }
            reader.readAsDataURL(file);
        }

        // Update label text on file choice
        $('#upload-chefe').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $('#label-chefe').html(fileName || 'Hili fail imajen...');
        });

        $('#upload-visto').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $('#label-visto').html(fileName || 'Hili fail imajen...');
        });

        // Trigger chefe signature application
        $('#btn-apply-chefe').on('click', function() {
            var fileInput = document.getElementById('upload-chefe');
            if (fileInput.files.length === 0) {
                alert('Favór hili fail imajen tanda tangan uluk lai!');
                return;
            }
            var file = fileInput.files[0];
            var reader = new FileReader();
            reader.onloadend = function() {
                var base64 = reader.result;
                var currentCode = $('#template_formatu').summernote('code');
                
                // Search patterns to replace
                var patterns = [
                    'https://your-link-tanda-tangan-chefe.jpg',
                    'https://your-link-tanda-tangan.jpg',
                    'your-link-tanda-tangan-chefe.jpg',
                    'your-link-tanda-tangan.jpg'
                ];
                
                var replaced = false;
                for (var i = 0; i < patterns.length; i++) {
                    if (currentCode.indexOf(patterns[i]) !== -1) {
                        currentCode = currentCode.split(patterns[i]).join(base64);
                        replaced = true;
                    }
                }
                
                if (replaced) {
                    $('#template_formatu').summernote('code', currentCode);
                    alert('Tanda tangan Chefe Suco substituído ho susesu iha template laran!');
                } else {
                    // Fallback to direct insertion at cursor position
                    var imgNode = $('<img>').attr('src', base64).css({
                        'max-width': '170px',
                        'max-height': '75px',
                        'margin': '8px 0',
                        'display': 'inline-block'
                    });
                    $('#template_formatu').summernote('insertNode', imgNode[0]);
                    alert('Tanda tangan Chefe Suco hatama foun ba iha cursor nia fatin!');
                }
            };
            reader.readAsDataURL(file);
        });

        // Trigger visto signature application
        $('#btn-apply-visto').on('click', function() {
            var fileInput = document.getElementById('upload-visto');
            if (fileInput.files.length === 0) {
                alert('Favór hili fail imajen tanda tangan uluk lai!');
                return;
            }
            var file = fileInput.files[0];
            var reader = new FileReader();
            reader.onloadend = function() {
                var base64 = reader.result;
                var currentCode = $('#template_formatu').summernote('code');
                
                // Search patterns to replace
                var patterns = [
                    'https://your-link-tanda-tangan-visto.jpg',
                    'your-link-tanda-tangan-visto.jpg'
                ];
                
                var replaced = false;
                for (var i = 0; i < patterns.length; i++) {
                    if (currentCode.indexOf(patterns[i]) !== -1) {
                        currentCode = currentCode.split(patterns[i]).join(base64);
                        replaced = true;
                    }
                }
                
                if (replaced) {
                    $('#template_formatu').summernote('code', currentCode);
                    alert('Tanda tangan Visto Posto Adm substituído ho susesu iha template laran!');
                } else {
                    // Fallback to direct insertion at cursor position
                    var imgNode = $('<img>').attr('src', base64).css({
                        'max-width': '170px',
                        'max-height': '75px',
                        'margin': '8px 0',
                        'display': 'inline-block'
                    });
                    $('#template_formatu').summernote('insertNode', imgNode[0]);
                    alert('Tanda tangan Visto Posto Adm hatama foun ba iha cursor nia fatin!');
                }
            };
            reader.readAsDataURL(file);
        });
    });
</script>
<?= $this->endSection() ?>
