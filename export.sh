PLUGINS='/Users/rbeall/Sites/ogmt_local/wp-content/plugins'
MYPLUGIN=$PLUGINS/${PWD##*/}
if [ -d $MYPLUGIN ]; then
  echo "${PWD##*/} Installed. Removing Plugin..."
  sudo rm -R $MYPLUGIN
fi
echo "Copying ${PWD##*/} to $PLUGINS"
sudo cp -R ../${PWD##*/} $MYPLUGIN
