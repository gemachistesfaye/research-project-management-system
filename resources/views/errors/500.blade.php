<!DOCTYPE html>
<html>
<head><title>500 Error</title></head>
<body style="font-family:monospace;padding:40px;background:#1a1a2e;color:#e0e0e0;">
<h1 style="color:#ff6b6b;">Server Error (500)</h1>
@if(isset($exception))
<h3>Error:</h3>
<pre style="background:#16213e;padding:15px;border-radius:8px;overflow-x:auto;color:#e94560;">{{ $exception->getMessage() }}</pre>
<h3>File:</h3>
<pre style="background:#16213e;padding:15px;border-radius:8px;color:#0f3460;">{{ $exception->getFile() }}:{{ $exception->getLine() }}</pre>
@endif
</body>
</html>
