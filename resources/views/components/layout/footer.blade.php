<footer>
  <div class="py-40 md:py-20 flex justify-center md:grid md:grid-cols-12 md:gap-x-16">
    <div class="md:col-span-1 md:col-start-2">
      <a href="https://www.instagram.com/fiefelstein/" 
        target="_blank" 
        title="Fiefelstein auf Instagram">
        <x-icons.instagram />
      </a>
    </div>
  </div>
</footer>
@livewireScripts
@vite('resources/js/app.js')
@if (app()->environment('production'))
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EJK5HMNWMD"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-EJK5HMNWMD');
</script>
@endif
</body>
</html>
<!-- made with ❤ by wbg.ch & marceli.to -->