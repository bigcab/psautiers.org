function getFlash()
{
	return document.Player;
}
function play(file)
{
	getFlash().playjs(file);
}
function stop()
{
	getFlash().stopjs();
}

function pause()
{
	getFlash().pausejs();;
}
function alertjs(x)
{
	alert(x);
}
