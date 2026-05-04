<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<title>Nova Solicitação de Reserva</title>
</head>
<body style="margin:0; padding:24px; background-color:#f8fafc; color:#0f172a; font-family:Arial, Helvetica, sans-serif;">
	<div style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
		<div style="padding:24px; background:#1e3a8a; color:#ffffff;">
			<h1 style="margin:0; font-size:24px;">Nova Solicitacao de Reserva</h1>
			<p style="margin:8px 0 0; font-size:14px; opacity:0.9;">Uma nova solicitacao foi enviada pelo site.</p>
		</div>

		<div style="padding:24px;">
			<h2 style="margin:0 0 12px; font-size:18px;">Produto</h2>
			<p style="margin:0 0 24px; font-size:15px;">{{ $product?->name ?? 'Nao informado' }}</p>

			<h2 style="margin:0 0 12px; font-size:18px;">Dados do Cliente</h2>
			<table style="width:100%; border-collapse:collapse; margin-bottom:24px; font-size:14px;">
				<tbody>
					<tr>
						<td style="padding:8px 0; font-weight:700; width:180px;">Nome</td>
						<td style="padding:8px 0;">{{ $booking->customer_name }}</td>
					</tr>
					<tr>
						<td style="padding:8px 0; font-weight:700;">E-mail</td>
						<td style="padding:8px 0;">{{ $booking->customer_email }}</td>
					</tr>
					<tr>
						<td style="padding:8px 0; font-weight:700;">Telefone</td>
						<td style="padding:8px 0;">{{ $booking->customer_phone }}</td>
					</tr>
					@if($booking->customer_company)
						<tr>
							<td style="padding:8px 0; font-weight:700;">Empresa</td>
							<td style="padding:8px 0;">{{ $booking->customer_company }}</td>
						</tr>
					@endif
				</tbody>
			</table>

			<h2 style="margin:0 0 12px; font-size:18px;">Endereco</h2>
			<table style="width:100%; border-collapse:collapse; margin-bottom:24px; font-size:14px;">
				<tbody>
					<tr>
						<td style="padding:8px 0; font-weight:700; width:180px;">CEP</td>
						<td style="padding:8px 0;">{{ $booking->customer_zip_code }}</td>
					</tr>
					<tr>
						<td style="padding:8px 0; font-weight:700;">Cidade</td>
						<td style="padding:8px 0;">{{ $booking->customer_city }}</td>
					</tr>
					<tr>
						<td style="padding:8px 0; font-weight:700;">Rua</td>
						<td style="padding:8px 0;">{{ $booking->customer_street }}</td>
					</tr>
					<tr>
						<td style="padding:8px 0; font-weight:700;">Numero</td>
						<td style="padding:8px 0;">{{ $booking->customer_number }}</td>
					</tr>
					<tr>
						<td style="padding:8px 0; font-weight:700;">Ponto de referencia</td>
						<td style="padding:8px 0;">{{ $booking->customer_reference }}</td>
					</tr>
				</tbody>
			</table>

			<h2 style="margin:0 0 12px; font-size:18px;">Periodo Solicitado</h2>
			<p style="margin:0 0 8px; font-size:14px;"><strong>Inicio:</strong> {{ $booking->start_date?->format('d/m/Y') }}</p>
			<p style="margin:0 0 24px; font-size:14px;"><strong>Termino:</strong> {{ ($booking->end_date ?? $booking->start_date)?->format('d/m/Y') }}</p>

			@if($booking->message)
				<h2 style="margin:0 0 12px; font-size:18px;">Mensagem</h2>
				<p style="margin:0 0 24px; font-size:14px; white-space:pre-line;">{{ $booking->message }}</p>
			@endif

			<p style="margin:0; font-size:14px;">
				<a href="{{ config('app.url') . '/admin/bookings' }}" style="display:inline-block; padding:12px 18px; background:#1e3a8a; color:#ffffff; text-decoration:none; border-radius:10px; font-weight:700;">
					Ver no Painel Administrativo
				</a>
			</p>
		</div>
	</div>
</body>
</html>
