<!DOCTYPE html>
<html>
<head>
    <title>Lamaran Pekerjaan</title>
</head>
<body>
    <h1>Lamaran Pekerjaan Baru</h1>
    <p>Lamaran baru telah diajukan untuk posisi: <strong>{{ $job_title }}</strong></p>
    <h2>Detail Pelamar</h2>
    <ul>
        <li><strong>Nama Lengkap:</strong> {{ $full_name }}</li>
        <li><strong>Email:</strong> {{ $email }}</li>
        <li><strong>Nomor Telepon:</strong> {{ $phone_number }}</li>
        <li><strong>CV:</strong> <a href="{{ $cv_url }}">{{ $cv_url }}</a></li>
        @if($portfolio_url)
            <li><strong>Portofolio:</strong> <a href="{{ $portfolio_url }}">{{ $portfolio_url }}</a></li>
        @endif
    </ul>
    <p>CV dan portofolio (jika ada) terlampir pada email ini.</p>
</body>
</html>