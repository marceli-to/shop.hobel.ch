<x-layout.app 
  :title="$product->name" 
  :backlink="route('page.category', ['category' => $category])">

  <x-grid.wrapper>
      
    <x-grid.span class="flex flex-col gap-y-20 lg:col-span-6">
      
    </x-grid.span>

    <x-grid.span class="lg:col-span-5 lg:col-start-8 px-20 lg:pl-0">
    </x-grid.span>

  </x-grid.wrapper>

</x-layout.app>
