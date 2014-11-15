<?php 
	class a{
        var $abc="ABC";
    }
    $b=new a();
    $c=$b;
    echo $b->abc;//这里输出ABC
    echo $c->abc;//这里输出ABC
    $b->abc="DEF";
    echo $c->abc;//这里输出DEF
?> 