<p>Hola {{ $reservation->user->person?->name ?? 'socio/a' }},</p>

<p>El ejemplar <strong>{{ $material->title }}</strong> ya no se encuentra disponible porque fue registrado como perdido.</p>

<p>Tu solicitud permanece en espera por si se incorpora otra copia disponible.</p>

<p>Biblioteca Archivium</p>
