<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - CinéHall</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .ticket { width: 300px; border: 1px solid #ccc; padding: 20px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .movie-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .info { margin-bottom: 5px; }
        .qr-code { text-align: center; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2>CinéHall</h2>
        </div>
        
        <div class="movie-title">{{ $ticket->reservation->screening->movie->title }}</div>
        
        <div class="info">
            <strong>Date:</strong> {{ $ticket->reservation->screening->start_time->format('d/m/Y H:i') }}
        </div>
        
        <div class="info">
            <strong>Salle:</strong> {{ $ticket->reservation->screening->hall->name }}
        </div>
        
        <div class="info">
            <strong>Place:</strong> Rang {{ $ticket->seat->row }}, Numéro {{ $ticket->seat->number }}
        </div>
        
        <div class="info">
            <strong>Prix:</strong> {{ number_format($ticket->price, 2) }} €
        </div>
        
        <div class="qr-code">
            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->generate($ticket->qr_code)) !!} ">
        </div>
        
        <div class="footer">
            Ticket #{{ $ticket->id }} - {{ $ticket->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>