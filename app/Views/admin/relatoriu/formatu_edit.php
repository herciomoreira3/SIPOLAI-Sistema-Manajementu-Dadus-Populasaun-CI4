<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        background: #ffffff !important;
    }
    .btn-rounded {
        border-radius: 10px !important;
        padding: 8px 20px !important;
        font-weight: 600 !important;
        letter-spacing: 0.2px;
        transition: all 0.2s ease !important;
    }
    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        border: none !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18) !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25) !important;
        transform: translateY(-1px) !important;
    }
    .btn-outline-secondary {
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        background: transparent !important;
    }
    .btn-outline-secondary:hover {
        background: #f8fafc !important;
        color: #1e293b !important;
    }
    @media print { .no-print { display: none !important; } }
</style>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-edit mr-2"></i> Konfigura Formatu COP: <?= esc($format['naran_relatoriu']) ?></h3>
            </div>
            <form action="<?= base_url('admin/formatu-relatoriu/' . $format['id_formatu_relatoriu'] . '/update') ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="template_cop" class="font-weight-bold text-muted">Template COP / Cabecalho (HTML/Text)</label>
                    <textarea name="template_cop" id="template_cop" class="form-control" rows="15" placeholder="Konfigura formatu COP relatoriu..."><?= esc($format['template_cop']) ?></textarea>
                    <small class="text-muted d-block mt-2">
                        Summernote rich-text editor ne'e permite ita boot sira atu bele hasai ka aumenta logo, muda formatu testu sira, aumenta liña separator, no seluk tan ba formatu COP relatoriu nian.
                    </small>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= route_to('formatu-relatoriu') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
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
        $('#template_cop').summernote({
            height: 400,
            placeholder: 'Prepara formatu cabecalho/COP relatoriu iha ne\'e...',
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
                    'max-width': '150px',
                    'height': 'auto',
                    'margin': '10px'
                });
                $('#template_cop').summernote('insertNode', imgNode[0]);
            }
            reader.readAsDataURL(file);
        }
    });
</script>
<?= $this->endSection() ?>
