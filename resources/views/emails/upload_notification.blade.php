<p>Your files have been uploaded successfully!</p>
<p>Download Link: <a href="{{ route('api.download', ['token' => $session->token]) }}">{{ route('api.download', ['token' => $session->token]) }}</a></p>
<p>Expires At: {{ $session->expires_at }}</p>