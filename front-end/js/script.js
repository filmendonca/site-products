// Fun��es para ir ao topo da p�gina

window.onscroll = function() {scrollUp()};

function scrollUp() {
  if (document.documentElement.scrollTop > 1000) {
    document.getElementById("top-button").style.display = "block";
  } 
  else {
    document.getElementById("top-button").style.display = "none";
  }
}

function buttontopScroll() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}