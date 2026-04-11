<x-mail::message>
# Nova Solicitação de Reserva

Olá! Uma nova solicitação de reserva foi recebida.

---

## Produto
**{{ $product?->name ?? 'Não informado' }}**

---

## Dados do Cliente

| Campo | Informação |
|-------|-----------|
| Nome | {{ $booking->customer_name }} |
| E-mail | {{ $booking->customer_email }} |
| Telefone | {{ $booking->customer_phone }} |
@if($booking->customer_company)
| Empresa | {{ $booking->customer_company }} |
@endif
@if($booking->customer_city)
| Cidade | {{ $booking->customer_city }} |
@endif

---

## Período Solicitado

**Data de início:** {{ $booking->start_date?->format('d/m/Y') }}
@if($booking->end_date)
**Data de término:** {{ $booking->end_date?->format('d/m/Y') }}
@endif

@if($booking->message)
---

## Mensagem do Cliente

{{ $booking->message }}
@endif

---

<x-mail::button :url="config('app.url') . '/admin/bookings'" color="primary">
Ver no Painel Administrativo
</x-mail::button>

Atenciosamente,
**{{ config('app.name') }}**
</x-mail::message>
