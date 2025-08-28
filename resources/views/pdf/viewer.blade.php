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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        #pdf-viewer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px; /* Space between pages */
        }

        canvas {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
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

        #error-container {
            display: none; /* Hidden by default */
            color: #f8d7da;
            background-color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        #error-container h2 {
            margin-top: 0;
            font-size: 24px;
        }

        #error-container p {
            font-size: 16px;
        }

        #error-container .error-details {
            font-family: monospace;
            font-size: 12px;
            color: #f5c6cb;
            margin-top: 20px;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            text-align: left;
            white-space: pre-wrap;
            word-break: break-all;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<div id="preloader">
    <div class="spinner"></div>
</div>

<div id="error-container">
    <h2>Could Not Load PDF</h2>
    <p>The document might be missing, corrupted, or not accessible.
        <br/> Kindly close the page and click the file again.
        <br/> Please try again later or contact support if the issue persists.</p>
{{--    <div class="error-details"></div>--}}
</div>

<div id="pdf-viewer"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
    const pdfUrl = "{{ $pdfUrl }}";
    const viewer = document.getElementById('pdf-viewer');
    const preloader = document.getElementById('preloader');
    const errorContainer = document.getElementById('error-container');

    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

    // Disable right-click context menu
    viewer.addEventListener('contextmenu', event => event.preventDefault());

    const loadingTask = pdfjsLib.getDocument(pdfUrl);

    loadingTask.promise.then(pdf => {
        // On success, render all pages
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            pdf.getPage(pageNum).then(page => {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                const viewport = page.getViewport({scale: 1.5});

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                page.render(renderContext).promise.then(() => {
                    // Hide preloader after the first page is rendered
                    if (page.pageNumber === 1) {
                        preloader.style.display = 'none';
                    }
                });

                viewer.appendChild(canvas);
            });
        }
    }, function (reason) {
        // On failure, display the custom error message
        console.error('PDF Loading Error:', reason);
        preloader.style.display = 'none'; // Hide the spinner

        const errorDetails = errorContainer.querySelector('.error-details');
        errorDetails.textContent = `Technical details: ${reason.message || reason}`;
        errorContainer.style.display = 'block'; // Show the error container
    });
</script>
</body>
</html>
