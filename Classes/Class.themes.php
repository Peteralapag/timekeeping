<?php
class Themes
{
	public function GetThemes($house)
	{
		include 'Models/Themes/'.$house.'.php';
	}
}