<?php
	session_start();
	if (!isset($_SESSION["language"])) {
		$_SESSION["language"] = "en";
	}

	($_SESSION["language"] == "vn") ? require("./localization/header.vn.php") : require("./localization/header.en.php");

	$returnUrl = "/change-language?returnUrl=";
	$returnUrl .= (isset($_SERVER["PATH_INFO"])) ? $_SERVER["PATH_INFO"] : "/";
	(isset($_SERVER["QUERY_STRING"])) && $returnUrl .= "?" . $_SERVER["QUERY_STRING"];
?>

<header class="main-header clearfix" role="header">

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
			<li><a href="/"><?php echo $content["home"] ?></a></li>
			<li><a href="/about"><?php echo $content["about"] ?></a></li>
			<li><a href="/courses"><?php echo $content["courses"] ?></a></li>
			<li><a href="/contact"><?php echo $content["contact"] ?></a></li>
			<li><a href="/applicant/jobsearch"><?php echo $content["jobsearch"] ?></a></li>
			<li><a href="/applicant/profile"><?php echo $content["profile"] ?></a></li>
			<li>
				<a href=<?php echo $returnUrl ?>>
					<span <?php echo(($_SESSION["language"] != "vn") ? 'class="unbolded"' : "")?>>VN</span> | 
					<span <?php echo(($_SESSION["language"] != "en") ? 'class="unbolded"' : "")?>>EN</span>
				</a>
			</li>
		</ul>
	</nav>

</header>