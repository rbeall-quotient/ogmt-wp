console.log("OGMT MINI FIELD SCRIPT LOADED")

function hide_non_minis(classname)
{
  console.log("hide_non_minis fired");
  console.log("Classname: " + classname);

  var expander = document.getElementById(classname + '-expander');
  var li = document.getElementById(classname);

  if(expander.textContent == 'Expand')
  {
    expander.textContent = "Collapse";
    li.classList.add('object-highlight');
  }
  else
  {
    expander.textContent = 'Expand';
    li.classList.remove('object-highlight');
  }

  var elements = document.getElementsByClassName(classname);

  console.log(elements.length);

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
