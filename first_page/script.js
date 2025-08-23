// Simple client-side gate for demo auth (non-sensitive). In production, replace with server auth.
(function(){
  const LS_KEY = 'mp_auth';
  const overlay = document.getElementById('loginOverlay');
  const form = document.getElementById('loginForm');
  const u = document.getElementById('loginUser');
  const p = document.getElementById('loginPass');
  const err = document.getElementById('loginError');
  const togglePw = document.getElementById('togglePw');
  const paramsBtn = document.getElementById('paramsBtn');
  const paramsMenu = document.getElementById('paramsMenu');
  const logoutBtn = document.getElementById('logoutBtn');
  const pmName = document.getElementById('paramsName');
  const pmMeta = document.getElementById('paramsMeta');
  const pmU = document.getElementById('pmUsername');
  const pmE = document.getElementById('pmEmail');

  function isAuthed(){
    try{ return JSON.parse(localStorage.getItem(LS_KEY)||'null'); }catch{return null}
  }
  function showOverlay(){ if(overlay) { overlay.classList.add('show'); overlay.setAttribute('aria-hidden','false'); } }
  function hideOverlay(){ if(overlay) { overlay.classList.remove('show'); overlay.setAttribute('aria-hidden','true'); } }
  function showParams(){ if(paramsBtn) paramsBtn.style.display='inline-flex'; }

  const auth = isAuthed();
  if(!auth){ showOverlay(); } else { showParams(); hydrateParams(auth); }

  function hydrateParams(data){
    if(pmName) pmName.textContent = data.full_name || 'Wail';
    if(pmMeta) pmMeta.textContent = 'Signed in';
    if(pmU) pmU.textContent = data.username || 'wail';
    if(pmE) pmE.textContent = data.email || 'wail@example.com';
  }

  if(togglePw && p){
    togglePw.addEventListener('click', ()=>{
      const isText = p.type==='text';
      p.type = isText ? 'password' : 'text';
      togglePw.setAttribute('aria-pressed', String(!isText));
      togglePw.textContent = isText ? 'Show' : 'Hide';
      p.focus();
    });
  }

  if(form){
    form.addEventListener('submit', (e)=>{
      e.preventDefault();
      err.style.display='none';
      const name = (u.value||'').trim();
      const pass = (p.value||'').trim();
      if(!name || !pass){ err.textContent='Please fill your username and password.'; err.style.display='block'; return; }
      // Demo credentials
      if(name.toLowerCase()==='wail' && pass==='wailelkaysany'){
        const data = { username:'wail', full_name:'Wail', email:'wail@example.com' };
        localStorage.setItem(LS_KEY, JSON.stringify(data));
        hideOverlay();
        showParams();
        hydrateParams(data);
      } else {
        err.textContent='Invalid name or password';
        err.style.display='block';
      }
    });
  }

  if(paramsBtn && paramsMenu){
    paramsBtn.addEventListener('click', ()=>{
      const open = paramsMenu.classList.contains('show');
      paramsMenu.classList.toggle('show', !open);
      paramsMenu.setAttribute('aria-hidden', String(open));
      paramsBtn.setAttribute('aria-expanded', String(!open));
    });
    document.addEventListener('click', (e)=>{
      if(!paramsMenu.classList.contains('show')) return;
      if(e.target===paramsBtn || paramsBtn.contains(e.target)) return;
      if(!paramsMenu.contains(e.target)){
        paramsMenu.classList.remove('show');
        paramsMenu.setAttribute('aria-hidden','true');
        paramsBtn.setAttribute('aria-expanded','false');
      }
    });
  }

  if(logoutBtn){
    logoutBtn.addEventListener('click', ()=>{
      localStorage.removeItem(LS_KEY);
      paramsMenu.classList.remove('show');
      paramsBtn.style.display='none';
      showOverlay();
      u.value=''; p.value='';
    });
  }
})();
