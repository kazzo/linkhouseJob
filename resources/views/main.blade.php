<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>DataTable</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">        
		<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
		<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- 
        @vite('resources/css/app.css')
-->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body>
<div id="app">
   <datatable-component dataUrl="https://app.linkhouse.co/rekrutacja/api " pingUrl="https://app.linkhouse.co/rekrutacja/ping" title="Lista użytkowników"></datatable-component>
</div>
<!-- 
@vite('resources/js/app.js')
-->
    </body>
</html>
