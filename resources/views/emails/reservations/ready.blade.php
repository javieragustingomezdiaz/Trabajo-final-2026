<p>Hola {{ $reservation->user->person?->name ?? 'socio/a' }},</p>

<p>Ya hay un ejemplar disponible para el material <strong>{{ $reservation->material->title }}</strong>.</p>

<p>Tenés hasta {{ $reservation->confirmation_expires_at->format('d/m/Y H:i') }} para confirmar la reserva. Si no confirmás dentro de esas seis horas, el ejemplar se ofrecerá al siguiente socio de la cola.</p>

<p><a href="{{ $url }}">Revisar y confirmar disponibilidad</a></p>

<p>Si no solicitaste esta reserva, podés ignorar este mensaje.</p>
