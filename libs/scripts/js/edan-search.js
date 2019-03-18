function edan_search_redirect(term)
{
  var search = document.getElementById('edan-search-bar').value;
  search = search.trim();
  search = search.replace(' ', '+');
  console.log(window.location.href);
  console.log(search);

  let url = new URL(window.location.href);
  let params = new URLSearchParams(url.search.slice(1));

  if(params.has('edan_q'))
  {
    params = new URLSearchParams();
    params.set('edan_q', search);
  }
  else
  {
    //Add a second foo parameter.
    params.append('edan_q', search);
  }

  console.log("URL: " + url.toString().split('?')[0]);
  console.log("PARAMS: " + params.toString());

  window.location.replace(url.toString().split('?')[0] + '?' + params.toString());

  return false;
}
