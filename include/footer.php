<!-- js para toogle del navbar -->

<script>
    document.getElementById('burger').addEventListener('click', () => {
        document.getElementById('menu').style.left = '0'
    })

    document.getElementById('cerrarMenu').addEventListener('click', () => {
        document.getElementById('menu').style.left = '-100%'
    })
</script>

</body>
</html>