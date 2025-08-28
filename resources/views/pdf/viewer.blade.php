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
    </style>
</head>
<body>
    <div id="pdf-viewer"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        const pdfUrl = "{{ $pdfUrl }}";

        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        const viewer = document.getElementById('pdf-viewer');

        // Disable right-click context menu on the viewer to prevent saving images
        viewer.addEventListener('contextmenu', event => event.preventDefault());

        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                pdf.getPage(pageNum).then(page => {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    // Adjust scale for better resolution
                    const viewport = page.getViewport({ scale: 1.5 });

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    viewer.appendChild(canvas);

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                });
            }
        });
    </script>
</body>
</html>
