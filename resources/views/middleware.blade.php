<html>
<body>
<script type="text/javascript">
    localStorage.setItem("api_token_uir", {!! json_encode($api_token) !!});
    setTimeout(function(){ window.location.href = {!! json_encode($redirect) !!}; } , 500);
</script>
</body>
</html>