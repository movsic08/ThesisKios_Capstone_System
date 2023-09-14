<div
    class="mt-2 flex rounded-md bg-white p-2 drop-shadow-lg duration-500 hover:-translate-y-1 lg:items-center lg:justify-center">
    {{-- svg  --}}
    <div class="hidden w-[2rem] lg:block">
        <svg fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M9.027 14.91c.168-.099.352-.195.551-.286-.168.25-.348.493-.54.727-.336.404-.597.62-.762.687a.312.312 0 0 1-.042.014.338.338 0 0 1-.031-.053c-.067-.132-.065-.26.048-.432.127-.198.383-.425.776-.658Zm2.946-1.977c-.142.03-.284.06-.427.094a25.2 25.2 0 0 0 .6-1.26c.19.351.394.695.612 1.03-.26.038-.523.083-.785.136Zm3.03 1.127a4.662 4.662 0 0 1-.522-.492c.274.006.521.026.735.065.38.068.559.176.621.25.02.021.031.049.032.077a.524.524 0 0 1-.072.24.368.368 0 0 1-.113.15.128.128 0 0 1-.083.017c-.108-.003-.31-.08-.598-.307Zm-2.67-5.695c-.048.293-.13.629-.24.995a5.82 5.82 0 0 1-.106-.416c-.092-.423-.105-.756-.056-.986.046-.212.132-.298.236-.34a.621.621 0 0 1 .174-.048.71.71 0 0 1 .038.238c.006.146-.008.332-.046.558v-.001Z">
            </path>
            <path fill-rule="evenodd"
                d="M7.2 2.4h9.6a2.4 2.4 0 0 1 2.4 2.4v14.4a2.4 2.4 0 0 1-2.4 2.4H7.2a2.4 2.4 0 0 1-2.4-2.4V4.8a2.4 2.4 0 0 1 2.4-2.4Zm.198 14.002c.108.216.276.412.525.503a.95.95 0 0 0 .696-.036c.382-.156.762-.523 1.112-.944.4-.48.82-1.112 1.225-1.812a13.979 13.979 0 0 1 2.396-.487c.36.46.732.856 1.092 1.14.336.264.724.484 1.121.5.216.011.43-.046.612-.165a1.24 1.24 0 0 0 .425-.499c.108-.217.174-.444.165-.676a1.013 1.013 0 0 0-.24-.621c-.27-.324-.715-.48-1.152-.558a6.91 6.91 0 0 0-1.602-.06 13.146 13.146 0 0 1-1.176-2.023c.3-.792.525-1.541.624-2.153a3.72 3.72 0 0 0 .058-.737 1.487 1.487 0 0 0-.152-.646.841.841 0 0 0-.573-.438c-.242-.051-.492 0-.72.093-.453.18-.692.564-.782.987-.088.408-.048.884.055 1.364.106.487.286 1.017.516 1.554a23.64 23.64 0 0 1-1.274 2.672 9.189 9.189 0 0 0-1.779.774c-.444.264-.839.576-1.076.944-.252.392-.33.857-.096 1.324Z"
                clip-rule="evenodd"></path>
        </svg>
    </div>
    <div>
        <a wire:navigate href="{{ route('view-document', ['reference' => $docuData->reference]) }}"
            class="whitespace-normal font-semibold text-secondary-color lg:text-sm">
            {{ $docuData->title }}</a>
        <div class="flex text-sm text-white">
            <p class="mx-1 rounded bg-blue-700 px-2">{{ $docuData->document_type }}</p>
            <p class="rounded bg-blue-900 px-2">{{ $docuData->course }}</p>
        </div>
        <div class="flex">
            <div class="w-1/4 lg:w-1/12">
                <svg viewBox="0 0 46 46" fill="none">
                    <path
                        d="M20.6999 36.7992C20.6999 36.7992 18.3999 36.7992 18.3999 34.4992C18.3999 32.1992 20.6999 25.2992 29.8999 25.2992C39.0999 25.2992 41.3999 32.1992 41.3999 34.4992C41.3999 36.7992 39.0999 36.7992 39.0999 36.7992H20.6999ZM29.8999 22.9992C31.7299 22.9992 33.4849 22.2723 34.7789 20.9783C36.0729 19.6843 36.7999 17.9292 36.7999 16.0992C36.7999 14.2692 36.0729 12.5142 34.7789 11.2202C33.4849 9.92618 31.7299 9.19922 29.8999 9.19922C28.0699 9.19922 26.3149 9.92618 25.0209 11.2202C23.7269 12.5142 22.9999 14.2692 22.9999 16.0992C22.9999 17.9292 23.7269 19.6843 25.0209 20.9783C26.3149 22.2723 28.0699 22.9992 29.8999 22.9992Z"
                        fill="black" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M16.5984 36.7998C16.2569 36.0819 16.0863 35.2947 16.1001 34.4998C16.1001 31.3833 17.6641 28.1748 20.5544 25.9438C19.112 25.4994 17.6093 25.2822 16.1001 25.2998C6.9001 25.2998 4.6001 32.1998 4.6001 34.4998C4.6001 36.7998 6.9001 36.7998 6.9001 36.7998H16.5984Z"
                        fill="black" />
                    <path
                        d="M14.95 23C16.475 23.0003 17.9376 22.3947 19.0161 21.3165C20.0946 20.2384 20.7007 18.776 20.7009 17.251C20.7012 15.726 20.0956 14.2633 19.0175 13.1848C17.9393 12.1063 16.4769 11.5003 14.9519 11.5C13.4269 11.5 11.9644 12.1058 10.886 13.1841C9.80771 14.2625 9.2019 15.725 9.2019 17.25C9.2019 18.775 9.80771 20.2375 10.886 21.3159C11.9644 22.3942 13.425 23 14.95 23Z"
                        fill="black" />
                </svg>
            </div>
            <ul class="flex w-auto flex-wrap text-sm">
                <li class="inline">{{ $docuData->author_1 }}
                <li class="inline">{{ $docuData->author_2 }}, </li>
                <li class="inline">{{ $docuData->author_3 }} </li>
                <li class="inline">{{ $docuData->author_4 }}</li>
                </li>
            </ul>
        </div>
        <div class="flex">
            <div class="w-1/4 lg:w-1/12">
                <svg viewBox="0 0 47 47" fill="none">
                    <path
                        d="M10.1201 8.28047C10.1201 7.67047 10.3624 7.08546 10.7938 6.65412C11.2251 6.22279 11.8101 5.98047 12.4201 5.98047H22.9675C23.2698 5.98048 23.5691 6.04008 23.8483 6.15584C24.1275 6.27161 24.3812 6.44127 24.5948 6.65514L40.6948 22.7551C41.1261 23.1865 41.3684 23.7715 41.3684 24.3814C41.3684 24.9914 41.1261 25.5764 40.6948 26.0077L30.1474 36.5551C29.716 36.9864 29.131 37.2287 28.5211 37.2287C27.9111 37.2287 27.3261 36.9864 26.8948 36.5551L10.7948 20.4551C10.5809 20.2415 10.4113 19.9879 10.2955 19.7087C10.1797 19.4294 10.1201 19.1301 10.1201 18.8279V8.28047ZM18.1701 17.4805C18.6232 17.4805 19.0718 17.3912 19.4904 17.2179C19.9089 17.0445 20.2893 16.7903 20.6096 16.47C20.93 16.1496 21.1841 15.7693 21.3575 15.3507C21.5309 14.9322 21.6201 14.4835 21.6201 14.0305C21.6201 13.5774 21.5309 13.1288 21.3575 12.7102C21.1841 12.2916 20.93 11.9113 20.6096 11.591C20.2893 11.2706 19.9089 11.0165 19.4904 10.8431C19.0718 10.6697 18.6232 10.5805 18.1701 10.5805C17.2551 10.5805 16.3776 10.944 15.7306 11.591C15.0836 12.238 14.7201 13.1155 14.7201 14.0305C14.7201 14.9455 15.0836 15.823 15.7306 16.47C16.3776 17.117 17.2551 17.4805 18.1701 17.4805Z"
                        fill="black" />
                    <path
                        d="M8.49277 21.604C8.06202 21.1727 7.82005 20.5881 7.82002 19.9786V8.2793C7.21002 8.2793 6.62501 8.52162 6.19367 8.95295C5.76234 9.38428 5.52002 9.9693 5.52002 10.5793V21.1286C5.52002 21.7381 5.76152 22.3227 6.19277 22.754L22.2928 38.854C22.7241 39.2853 23.3091 39.5276 23.9191 39.5276C24.529 39.5276 25.114 39.2853 25.5454 38.854L25.645 38.7543L8.49277 21.604Z"
                        fill="black" />
                </svg>
            </div>
            <ul class="flex w-auto flex-wrap text-sm">
                <li class="inline">{{ $docuData->keyword_1 }}; </li>
                <li class="inline">{{ $docuData->keyword_2 }}; </li>
                <li class="inline">{{ $docuData->keyword_3 }}; </li>
                <li class="inline">{{ $docuData->keyword_4 }}; </li>
                <li class="inline">{{ $docuData->keyword_5 }}; </li>
                <li class="inline">{{ $docuData->keyword_6 }}; </li>
                <li class="inline">{{ $docuData->keyword_7 }}; </li>
                <li class="inline">{{ $docuData->keyword_8 }}; </li>
            </ul>
        </div>
        <div class="flex justify-between">
            <div class="lg: flex justify-center gap-1 lg:items-center">
                <p class="rounded bg-cyan-700 px-2">Citation
                </p>
                <span class="hidden text-xs md:block">10 Citation</span>
            </div>
            <div class="lg: flex justify-center gap-1 lg:items-center">
                <p class="rounded bg-blue-700 px-2">Citation
                </p>
                <span class="hidden text-xs md:block">10 Citation</span>
            </div>
            <div class="lg: flex justify-center gap-1 lg:items-center">
                <p class="rounded bg-yellow-700 px-2">Citation
                </p>
                <span class="hidden text-xs md:block">10 Citation</span>
            </div>
            <div class="lg: flex justify-center gap-1 lg:items-center">
                <p class="rounded bg-yellow-950 px-2">Bookmark
                </p>
            </div>
        </div>
    </div>
</div>
