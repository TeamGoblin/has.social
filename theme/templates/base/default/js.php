<!-- Scripts -->
<script>
window.dataJET = {
<?php
	if (!empty($jwt)) {
		echo "'jwt':'". $jwt ."'";	
	}
?>

};
</script>
<!-- jQuery first, then Bootstrap JS. -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jet.js"></script>

<?php foreach ($_jet_js as $_js) {
	echo "<script src=\"".$_js."\"></script>\n";
}