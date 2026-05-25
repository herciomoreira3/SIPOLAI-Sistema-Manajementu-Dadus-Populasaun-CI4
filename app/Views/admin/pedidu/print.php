<!DOCTYPE html>
<html lang="tet">
<head>
    <meta charset="UTF-8">
    <title>Print - <?= esc($pedidu['naran_pedidu']) ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        @page {
            size: A4;
            margin: 10mm 15mm 8mm 15mm; /* Enforces clean top and bottom margins */
        }
        @media print {
            body {
                background: none;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            html, body {
                height: 99%;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #f4f6f9; padding: 15px; border-bottom: 1px solid #ddd; text-align: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <button onclick="window.print();" style="background: #28a745; color: #fff; border: none; padding: 10px 25px; font-size: 14px; font-weight: bold; border-radius: 30px; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-right: 10px; font-family: inherit;">
             Imprime Karta
        </button>
        <button onclick="window.close();" style="background: #6c757d; color: #fff; border: none; padding: 10px 25px; font-size: 14px; font-weight: bold; border-radius: 30px; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-family: inherit;">
            Taka
        </button>
    </div>
    
    <div style="max-width: 800px; margin: 0 auto; padding: 10px;">
        <?= $parsed_template ?>
    </div>

    <script>
        // Auto-trigger window print on page load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
