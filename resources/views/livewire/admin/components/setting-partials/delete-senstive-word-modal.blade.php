  <div class="fixed right-0 top-0 z-30 flex h-screen w-screen items-center justify-center bg-gray-300 bg-opacity-25 backdrop-blur-sm lg:items-start"
      x-data="{ deleteSW: false }" x-show="deleteSW" x-on:open-sw.window = "deleteSW = true"
      x-on:close-sw.window = "deleteSW = false" x-on:keydown.escape.window = "$wire.closedeleteSW()"
      x-transition:enter.duration.400ms x-transition:leave.duration.300ms style="display: none">
      <div class="mx-4 md:flex md:items-center md:justify-center">
          <section class="h-fit w-full rounded-lg bg-white px-6 py-4 drop-shadow-xl md:mt-32">
              <strong class="w-full text-center text-red-700">Are you sure you want to delete this word
                  ?</strong>
              <div class="mt-4 px-4">
                  <p class="flex w-full items-center justify-center">
                      <strong class="rounded-lg bg-red-50 px-2 py-1 text-red-700">{{ $sensitiveWordTemp }}</strong>
                  </p>
                  <div class="mt-6 flex w-full flex-col gap-2 md:flex-row">
                      <button wire:click='closedeleteSW'
                          class="w-full rounded-lg border border-red-700 px-2 py-1 text-red-700 duration-300 ease-in-out hover:bg-red-700 hover:text-white">Cancel</button>
                      <BUtton wire:click='deleteSWConfirmed' wire:loading.attr='disabled'
                          class="w-full rounded-lg bg-primary-color px-2 py-1 text-white duration-300 ease-in-out hover:bg-blue-900">Delete</BUtton>
                  </div>
              </div>
          </section>
      </div>
  </div>
