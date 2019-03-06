/**
 * Hide or reveal non-mini elements.
 * @param  {[string]}  classname class name for non-mini classes
 */
function toggle_non_minis(classname)
{
  //get expander element for object
  var expander = document.getElementById(classname + '-expander');
  //get object element
  var li = document.getElementById(classname);

  //toggle text for expander between 'Expand' and 'Collapse'
  if(expander.textContent == 'Expand')
  {
    expander.textContent = "Collapse";

    //highlight object if expanded
    li.classList.add('object-highlight');
  }
  else
  {
    expander.textContent = 'Expand';

    //remove highlight if collapsed
    li.classList.remove('object-highlight');
  }

  //get fields elements
  var elements = document.getElementsByClassName(classname);

  //iterate through elements and display if hidden. Hide if currently displayed. 
  for(var i = 0; i < elements.length; i++)
  {
    if (elements[i].style.display === "none")
    {
      elements[i].style.display = "block";
    }
    else
    {
      elements[i].style.display = "none";
    }
  }
}
