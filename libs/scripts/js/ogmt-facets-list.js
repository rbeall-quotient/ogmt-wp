/**
 * Toggle display of facet filters based on link click
 * @param  {[string]} key name of facet
 */
function toggle_facet_view(key)
{
  //get facet filter list
  var element = document.getElementById('facet-' + key);

  //if display is "none", set to "block". Otherwise, set to "none".
  if(element.style.display === 'none')
  {
    element.style.display = 'block';
  }
  else
  {
    element.style.display = 'none';
  }
}
