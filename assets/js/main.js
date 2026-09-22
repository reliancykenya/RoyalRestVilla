document.addEventListener('DOMContentLoaded',()=>{
  const body=document.body;
  const header=document.querySelector('.rrv-header');
  const menuButton=document.querySelector('.rrv-menu-toggle');
  const nav=document.querySelector('.rrv-nav');

  if(menuButton&&nav){
    menuButton.addEventListener('click',()=>{
      const open=nav.classList.toggle('is-open');
      menuButton.setAttribute('aria-expanded',open?'true':'false');
    });
    nav.querySelectorAll('a[href*="#"]').forEach(link=>link.addEventListener('click',()=>{
      nav.classList.remove('is-open');
      menuButton.setAttribute('aria-expanded','false');
    }));
  }

  const setHeader=()=>header&&header.classList.toggle('is-scrolled',window.scrollY>20);
  setHeader();
  window.addEventListener('scroll',setHeader,{passive:true});

  const sections=[...document.querySelectorAll('section[id]')];
  const navLinks=[...document.querySelectorAll('.rrv-nav a[href*="#"]')];
  if('IntersectionObserver' in window&&sections.length){
    const observer=new IntersectionObserver(entries=>{
      const visible=entries.filter(e=>e.isIntersecting).sort((a,b)=>b.intersectionRatio-a.intersectionRatio)[0];
      if(!visible)return;
      navLinks.forEach(link=>{
        const id=(link.getAttribute('href').split('#')[1]||'');
        link.classList.toggle('is-active',id===visible.target.id);
      });
    },{rootMargin:'-30% 0px -55% 0px',threshold:[0,.15,.4,.7]});
    sections.forEach(section=>observer.observe(section));
  }

  const quickIn=document.getElementById('rrv-quick-checkin');
  const quickOut=document.getElementById('rrv-quick-checkout');
  const quickGuests=document.getElementById('rrv-quick-guests');
  const quickRoom=document.getElementById('rrv-quick-room');
  const bookIn=document.getElementById('rrv-book-checkin');
  const bookOut=document.getElementById('rrv-book-checkout');
  const bookGuests=document.getElementById('rrv-book-guests');
  const bookRoom=document.getElementById('rrv-book-room');

  const setCheckoutMin=(checkin,checkout)=>{
    if(!checkin||!checkout||!checkin.value)return;
    const d=new Date(checkin.value+'T12:00:00');
    d.setDate(d.getDate()+1);
    const min=d.toISOString().slice(0,10);
    checkout.min=min;
    if(checkout.value&&checkout.value<=checkin.value)checkout.value='';
  };
  if(quickIn&&quickOut)quickIn.addEventListener('change',()=>setCheckoutMin(quickIn,quickOut));
  if(bookIn&&bookOut)bookIn.addEventListener('change',()=>setCheckoutMin(bookIn,bookOut));

  const scrollToBook=()=>{
    const target=document.getElementById('book');
    if(target)target.scrollIntoView({behavior:'smooth',block:'start'});
  };

  const quickButton=document.querySelector('[data-rrv-quick-book]');
  if(quickButton){
    quickButton.addEventListener('click',()=>{
      if(quickIn&&bookIn)bookIn.value=quickIn.value;
      if(quickOut&&bookOut)bookOut.value=quickOut.value;
      if(quickGuests&&bookGuests)bookGuests.value=quickGuests.value;
      if(quickRoom&&bookRoom)bookRoom.value=quickRoom.value;
      if(bookIn&&bookOut)setCheckoutMin(bookIn,bookOut);
      scrollToBook();
    });
  }

  document.querySelectorAll('[data-rrv-room]').forEach(button=>{
    button.addEventListener('click',()=>{
      if(bookRoom)bookRoom.value=button.getAttribute('data-rrv-room')||'Any available room';
      scrollToBook();
    });
  });

  const lightbox=document.querySelector('.rrv-lightbox');
  const lightboxImage=lightbox?lightbox.querySelector('img'):null;
  const closeLightbox=()=>{
    if(!lightbox)return;
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden','true');
    body.classList.remove('rrv-lightbox-open');
    if(lightboxImage)lightboxImage.src='';
  };
  document.querySelectorAll('[data-rrv-lightbox]').forEach(button=>{
    button.addEventListener('click',()=>{
      if(!lightbox||!lightboxImage)return;
      lightboxImage.src=button.getAttribute('data-rrv-lightbox')||'';
      lightbox.classList.add('is-open');
      lightbox.setAttribute('aria-hidden','false');
      body.classList.add('rrv-lightbox-open');
    });
  });
  if(lightbox){
    lightbox.addEventListener('click',e=>{if(e.target===lightbox||e.target.closest('.rrv-lightbox__close'))closeLightbox();});
    document.addEventListener('keydown',e=>{if(e.key==='Escape')closeLightbox();});
  }

  if(location.hash==='#book'&&document.getElementById('book')){
    requestAnimationFrame(()=>document.getElementById('book').scrollIntoView({block:'start'}));
  }
});
