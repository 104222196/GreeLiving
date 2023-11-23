<?php
	($_SESSION["language"] == "vn") ? require("./localization/vn.php") : require("./localization/en.php");

	$returnUrl = "/change-language?returnUrl=";
	$returnUrl .= (isset($_SERVER["PATH_INFO"])) ? $_SERVER["PATH_INFO"] : "/";
	(isset($_SERVER["QUERY_STRING"])) && $returnUrl .= "?" . $_SERVER["QUERY_STRING"];
?>

<header class="main-header clearfix" role="header" id="header">

	<div class="logo">
		<a href="#">
			<img src="/assets/images/greeliving-logo.png" alt="Greeliving Learning Hub Logo" width="180" />
		</a>
	</div>

	<a href="#menu" class="menu-link">
		<i class="fa fa-bars"></i>
	</a>

	<nav id="menu" class="main-nav" role="navigation">
		<ul class="main-menu">
			<li><a href="/applicant/home"><?=$content["home"] ?></a></li>
			<li><a href="/about"><?=$content["about"] ?></a></li>
			<li><a href="/contact"><?=$content["contact"] ?></a></li>
			<li><a href="/applicant/courses"><?=$content["courses"] ?></a></li>
			<li><a href="/applicant/jobsearch"><?=$content["jobsearch"] ?></a></li>
			<li><a href="/applicant/profile"><?=$content["profile"] ?></a></li>
			<li>
				<a href=<?=$returnUrl ?>>
					<span <?=$_SESSION["language"] != "vn" ? 'class="unbolded"' : ""?>>VN</span> | 
					<span <?=$_SESSION["language"] != "en" ? 'class="unbolded"' : ""?>>EN</span>
				</a>
			</li>
			<li><a href="/employer/home"><?=$content["toEmployer"] ?></a></li>
		</ul>
	</nav>

</header>