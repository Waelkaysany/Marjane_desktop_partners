(function(){
  const btn = document.getElementById('paramsBtn');
  const menu = document.getElementById('paramsMenu');
  if(!btn || !menu) return;
  function open(){menu.classList.add('show');menu.setAttribute('aria-hidden','false');btn.setAttribute('aria-expanded','true');}
  function close(){menu.classList.remove('show');menu.setAttribute('aria-hidden','true');btn.setAttribute('aria-expanded','false');}
  btn.addEventListener('click',()=>{menu.classList.contains('show')?close():open();});
  document.addEventListener('keydown',(e)=>{if(e.key==='Escape'&&menu.classList.contains('show')) close();});
  document.addEventListener('click',(e)=>{if(!menu.classList.contains('show')) return; if(e.target===btn||btn.contains(e.target)) return; if(!menu.contains(e.target)) close();});
})();
