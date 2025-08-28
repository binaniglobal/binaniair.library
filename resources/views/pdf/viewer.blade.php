<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #525659; /* Dark background for contrast */
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align to the top */
        }
        #pdf-viewer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px; /* Space between pages */
        }
        canvas {
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            max-width: 100%; /* Ensure canvas is responsive */
            height: auto;
        }
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(82, 86, 89, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="spinner"></div>
    </div>
    <div id="pdf-viewer"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        const pdfUrl = "{{ $pdfUrl }}";
        const viewer = document.getElementById('pdf-viewer');
        const preloader = document.getElementById('preloader');

        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        viewer.addEventListener('contextmenu', event => event.preventDefault());

        const loadingTask = pdfjsLib.getDocument(pdfUrl);

        loadingTask.promise.then(pdf => {
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                pdf.getPage(pageNum).then(page => {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    const viewport = page.getViewport({ scale: 1.5 });

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    const renderTask = page.render(renderContext);
                    renderTask.promise.then(() => {
                        if (page.pageNumber === 1) {
                            preloader.style.display = 'none';
                        }
                    });

                    viewer.appendChild(canvas);
                });
            }
        }, function (reason) {
            console.error(reason);
            preloader.innerHTML = 'Error loading PDF.';
        });
    </script>
</body>
</html>
