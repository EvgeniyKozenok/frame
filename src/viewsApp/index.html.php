<html>
<head>
    <title>{{title}}</title>
    <style>{% include 'css/index.css' %}</style>
</head>
<body>
<div class="header">{% include 'header.html.php' %}</div>
<div class="sidebar">
    {% include 'sidebar.html.php' %}
</div>
<div class="content">
    {% include 'content.html.php' %}
</div>
<div class="footer">&copy; author</div>
</body>
</html>