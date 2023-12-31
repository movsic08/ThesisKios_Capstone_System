<x-app-layout>
    @section('title', 'Pdf - ' . $titleOfDocu)
    <section>

        <div id="pdfData" data-file-path="{{ asset('storage/' . $pdfFile) }}"></div>
        {{-- data-file-path="{{ auth()->user()->is_admin == 1 ? asset('storage/' . $this->pdfFile) : asset('storage/' . decrypt($this->pdfFile)) }}"> --}}
        <div class="container relative">
            <div class="" style="height: 100%; width: 100%;">
                {{-- sticky top --}}
                <div class="fixed inset-x-0 bottom-3 z-50 flex w-full items-center justify-center">
                    <div
                        class="flex w-fit items-center gap-1 rounded-md bg-slate-100 bg-opacity-70 px-1 outline outline-gray-200 drop-shadow-lg backdrop-blur-lg md:gap-2 md:px-9 md:py-1 lg:gap-3 lg:py-2">
                        <button class="rounded-md bg-primary-color px-2 py-1 font-bold text-white"
                            id="prevPageBtn">Prev</button>
                        <div class="flex items-center text-sm font-bold text-gray-600">
                            <x-label-input class="mr-1 text-sm">Pages</x-label-input>
                            <input type="number" id="pageNumberInput" value="1"
                                class="h-9 w-12 rounded-md border-2 bg-slate-100 px-2 text-center focus:outline-blue-950"
                                min="1" inputmode="numeric">
                            <span class="mx-1">/</span>
                            <span id="totalPages" class="m-2">10</span>
                        </div>
                        {{-- drop down --}}
                        <div x-data="{ isOpen: false, selectedOption: 'Fit to Width' }" class="relative inline-block text-left">
                            <button @click="isOpen = !isOpen" type="button" id="zoomDropdown"
                                class="rounded-lg bg-gray-200 px-1 text-gray-700 shadow-md md:px-2">
                                <span class="text-xs md:text-base" x-text="selectedOption "></span>
                                <svg class="ml-2 hidden h-5 w-5 md:inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="isOpen" @click.away="isOpen = false"
                                class="absolute right-0 mt-2 w-36 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                                x-bind:style="{ 'bottom': isOpen ? '0' : 'auto' }">
                                <div class="bg-gray-200 py-1" role="menu" aria-orientation="vertical"
                                    aria-labelledby="zoomDropdown">
                                    <a @click="selectedOption = 'Fit to Page'; isOpen=false;"
                                        class="dropdown-item hover-bg-gray-100 block px-4 py-2 text-sm text-gray-700"
                                        href="#" role="menuitem" id="fitToPage">Fit to Page</a>
                                    <a @click="selectedOption = 'Fit to Width'; isOpen=false;"
                                        class="dropdown-item hover-bg-gray-100 block px-4 py-2 text-sm text-gray-700"
                                        href="#" role="menuitem" id="fitToWidth">Fit to Width</a>
                                </div>
                            </div>
                        </div>

                        <button class="rounded-md bg-primary-color px-2 py-1 font-bold text-white"
                            id="nextPageBtn">Next</button>
                    </div>
                </div>

                <div class="col-span-12 mt-2 overflow-auto text-center" id="scrollableCanvas">
                    <div class="h-full w-auto">
                        <canvas class="mx-auto" id="pdfArea" style="height: auto; width: 100%;"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Set initial zoom level and limits
                let zoomLevel = 1.0;
                const minZoom = 0.5; // Minimum zoom level
                const maxZoom = 2.0; // Maximum zoom level
                const canvas = document.getElementById('pdfArea');
                // Function to change the canvas size based on the selected option
                function changeCanvasSize(size) {
                    if (canvas) {
                        if (size === 'fitToPage') {
                            canvas.style.height = '100%';
                            canvas.style.width = 'auto';
                        } else if (size === 'fitToWidth') {
                            canvas.style.height = 'auto';
                            canvas.style.width = '100%';
                        } else {
                            // Zoom in or out based on the given percentage
                            const percentage = parseFloat(size.replace('zoom', '')) / 100;
                            zoomLevel = percentage;
                            zoomLevel = Math.min(maxZoom, Math.max(minZoom, zoomLevel)); // Limit zoom level
                            canvas.style.height = (canvas.height * zoomLevel) + 'px';
                            canvas.style.width = (canvas.width * zoomLevel) + 'px';
                        }
                    }
                }
                // Handle dropdown item click events
                $('.dropdown-item').on('click', function(e) {
                    e.preventDefault();
                    const selectedSize = $(this).attr('id');
                    changeCanvasSize(selectedSize);
                });
                // Zoom In and Out with Shift+ and Shift+-
                $(document).on('keydown', function(e) {
                    if (e.shiftKey) {
                        if (e.key === '+') {
                            e.preventDefault();
                            zoomIn();
                        } else if (e.key === '_') { // Use '_' for 'Shift+-'
                            e.preventDefault();
                            zoomOut();
                        }
                    }
                });
                // Function to zoom in
                function zoomIn() {
                    zoomLevel = zoomLevel * 1.1;
                    zoomLevel = Math.min(maxZoom, zoomLevel); // Limit zoom level
                    canvas.style.height = (canvas.height * zoomLevel) + 'px';
                    canvas.style.width = (canvas.width * zoomLevel) + 'px';
                }
                // Function to zoom out
                function zoomOut() {
                    zoomLevel = zoomLevel / 1.1;
                    zoomLevel = Math.max(minZoom, zoomLevel); // Limit zoom level
                    canvas.style.height = (canvas.height * zoomLevel) + 'px';
                    canvas.style.width = (canvas.width * zoomLevel) + 'px';
                }
                // Enable pinch-to-zoom using Hammer.js
                const mc = new Hammer.Manager(canvas);
                const pinch = new Hammer.Pinch();
                mc.add([pinch]);
                let initialZoom = 1.0;
                mc.on('pinch', function(e) {
                    const newZoom = initialZoom * e.scale;
                    if (newZoom >= minZoom && newZoom <= maxZoom) {
                        canvas.style.height = (canvas.height * newZoom) + 'px';
                        canvas.style.width = (canvas.width * newZoom) + 'px';
                        zoomLevel = newZoom;
                    }
                });
                mc.on('pinchend', function(e) {
                    initialZoom = zoomLevel;
                });
            });
        </script>

        {{-- {!! $pdfViewerContent !!} --}}

    </section>
</x-app-layout>
