@extends('front.layouts.app')

@section('content')
    {{-- <div class="tour-details__header"
        style="background-image: url({{ $profil->banner ? asset('upload/profil/' . $profil->banner) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }});">
        <div class="container">
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><a href="{{ route('tour.index') }}">Tours</a></li>
                <li><span>{{ $tour->name }}</span></li>
            </ul>
        </div>
    </div> --}}

    <section class="tour-two tour-list tour-details-two">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="tour-details__content">
                        <div class="tour-two__top">
                            <div class="tour-two__top-left">
                                <h3>{{ $tour->name }}</h3>
                            </div>
                            <div class="tour-two__right">
                                <p><span>Rp. {{ number_format($tour->price ?? 0, 0, ',', '.') }}</span> <br>
                                    {{ $tour->price_label ?? 'Per Ticket' }}</p>
                            </div>
                        </div>
                        <style>
                            .tour-one__meta-label {
                                display: inline-block;
                                width: 90px;
                                font-weight: 600;
                                margin-right: 8px;
                                color: #333;
                            }

                            .tour-one__meta li {
                                margin-bottom: 8px;
                            }
                        </style>
                        <ul class="tour-one__meta list-unstyled">
                            <li>
                                <a href="#"><span class="">Durasi:</span> <i class="far fa-clock"></i>
                                    {{ $tour->duration_minutes }} minutes</a>
                            </li>
                            <li>
                                <a href="#"><span class="">Peserta:</span> <i class="far fa-user-circle"></i>
                                    {{ $tour->max_participants }}</a>
                            </li>
                            <li>
                                <a href="#"><span class="">Lokasi:</span> <i class="far fa-map"></i>
                                    {{ $tour->location }}</a>
                            </li>
                        </ul>

                        <h3 class="tour-details__title">Pilihan Rute</h3>

                        <div class="tour-details__plan">
                            @php($routes = isset($tour) && $tour->relationLoaded('routes') ? $tour->routes : $tour->routes ?? collect())
                            @forelse($routes as $idx => $route)
                                <div class="tour-details__plan-single">
                                    <div class="tour-details__plan-count">
                                        {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="tour-details__plan-content">
                                        <p style="padding-top: 10px;">{{ $route->route_name }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="tour-details__plan-single">
                                    <div class="tour-details__plan-count">01</div>
                                    <div class="tour-details__plan-content">
                                        <h3>Rute belum tersedia</h3>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="tour-details__spacer"></div>
                        <h3 class="tour-details__title">Fasilitas</h3>
                        <p>{!! $tour->short_description !!}</p>
                        {{-- <h3 class="tour-details__subtitle">Included/Exclude</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="tour-details__list list-unstyled">
                                    <li>
                                        <i class="fa fa-check"></i>
                                        Pick and Drop Services
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i>
                                        1 Meal Per Destination
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i>
                                        Cruise Dinner & Music Event
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i>
                                        Visit 7 Best Places in the City With Group
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="tour-details__list unavailable list-unstyled">
                                    <li>
                                        <i class="fa fa-times"></i>
                                        Additional Services
                                    </li>
                                    <li>
                                        <i class="fa fa-times"></i>
                                        1 Meal Per Destination
                                    </li>
                                    <li>
                                        <i class="fa fa-times"></i>
                                        Insurance
                                    </li>
                                    <li>
                                        <i class="fa fa-times"></i>
                                        Food & Drinks
                                    </li>
                                </ul>
                            </div>
                        </div> --}}
                        {{--                        
                        <div class="tour-details__spacer"></div>
                        <h3 class="tour-details__title">Tour Location</h3>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4562.753041141002!2d-118.80123790098536!3d34.152323469614075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80e82469c2162619%3A0xba03efb7998eef6d!2sCostco+Wholesale!5e0!3m2!1sbn!2sbd!4v1562518641290!5m2!1sbn!2sbd"
                            class="google-map__contact google-map__tour-details" allowfullscreen></iframe> --}}

                    </div>

                </div>
                <div class="col-lg-4">

                    <div class="tour-sidebar__search tour-sidebar__single">
                        <h3>Pesan Jeep Merapi</h3>
                        <form action="https://wa.me/{{ $profil->no_wa ?? '' }}" method="get" target="_blank"
                            class="tour-sidebar__search-form">
                            <div class="input-group">
                                <input type="text" name="name" placeholder="Nama Anda" required>
                            </div>
                            <div class="input-group">
                                <input type="email" name="email" placeholder="Alamat Email" required>
                            </div>
                            <div class="input-group">
                                <input type="tel" name="phone" placeholder="Telepon">
                            </div>
                            <div class="input-group">
                                <input type="text" name="order_date" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}"
                                    class="form-control" placeholder="Tanggal Order (dd/mm/yyyy)" readonly>
                            </div>
                            <div class="input-group">
                                <input type="text" name="trip_date" class="form-control" placeholder="Pilih Jadwal Trip"
                                    onfocus="(this.type='date')" onblur="(this.value ? this.type='date' : this.type='text')"
                                    required>
                            </div>
                            <div class="input-group">
                                <input type="text" name="tickets" class="form-control js-only-digits"
                                    data-price="{{ $tour->price ?? 0 }}" value="1" placeholder="Jumlah Tiket"
                                    inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>

                            </div>
                            <script>
                                (function() {
                                    var el = document.querySelector('.js-only-digits');
                                    if (!el) return;
                                    // sanitize on input: keep digits only
                                    el.addEventListener('input', function(e) {
                                        var v = e.target.value.replace(/[^0-9]/g, '');
                                        // prevent leading zeros turning empty when min is effectively 1
                                        e.target.value = v;
                                    });
                                    // prevent non-digit key presses except control keys
                                    el.addEventListener('keypress', function(e) {
                                        var c = e.which || e.keyCode;
                                        if (c === 13) return; // allow enter
                                        var ch = String.fromCharCode(c);
                                        if (!/[0-9]/.test(ch)) {
                                            e.preventDefault();
                                        }
                                    });
                                    // prevent paste of non-digits
                                    el.addEventListener('paste', function(e) {
                                        var text = (e.clipboardData || window.clipboardData).getData('text');
                                        if (/[^0-9]/.test(text)) {
                                            e.preventDefault();
                                        }
                                    });
                                })();
                            </script>
                            <div class="input-group">
                                <div id="total-price" style="font-weight:700">0</div>
                            </div>
                            <div class="input-group">
                                <textarea name="message" placeholder="Pesan (opsional)"></textarea>
                            </div>
                            <div class="input-group">
                                <button type="submit" class="thm-btn">Pesan via WhatsApp</button>
                            </div>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var form = document.querySelector('.tour-sidebar__search-form');
                                if (!form) return;
                                var tickets = form.querySelector('input[name="tickets"]');
                                var totalEl = document.getElementById('total-price');
                                var price = parseFloat(tickets.dataset.price) || 0;

                                function formatIDR(n) {
                                    return 'Rp. ' + Number(n).toLocaleString('id-ID');
                                }

                                function updateTotal() {
                                    var val = tickets.value;
                                    if (val === '') {
                                        totalEl.textContent = '0 x ' + formatIDR(price) + ' = ' + formatIDR(0);
                                        return;
                                    }
                                    var qty = parseInt(val) || 0;
                                    var total = qty * price;
                                    totalEl.textContent = qty + ' x ' + formatIDR(price) + ' = ' + formatIDR(total);
                                }
                                tickets.addEventListener('input', updateTotal);
                                tickets.addEventListener('blur', function() {
                                    if (!tickets.value || parseInt(tickets.value) < 1) {
                                        tickets.value = 1;
                                        updateTotal();
                                    }
                                });
                                updateTotal();

                                // Override submit to send all values as WhatsApp text
                                form.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    var name = form.querySelector('input[name="name"]').value.trim();
                                    var email = form.querySelector('input[name="email"]').value.trim();
                                    var phone = form.querySelector('input[name="phone"]').value.trim();
                                    var orderDate = form.querySelector('input[name="order_date"]').value.trim();
                                    var tripIso = form.querySelector('input[name="trip_date"]').value.trim();
                                    var qty = parseInt(tickets.value) || 1;
                                    if (qty < 1) qty = 1;
                                    var note = form.querySelector('textarea[name="message"]').value.trim();

                                    function formatIndoFromISO(iso) {
                                        if (!iso) return '';
                                        var d = new Date(iso);
                                        if (isNaN(d)) return iso;
                                        var dd = String(d.getDate()).padStart(2, '0');
                                        var mm = String(d.getMonth() + 1).padStart(2, '0');
                                        var yyyy = d.getFullYear();
                                        return dd + '/' + mm + '/' + yyyy;
                                    }
                                    var tripDate = formatIndoFromISO(tripIso);

                                    var tourName = "{{ $tour->name }}";
                                    var unitPrice = price;
                                    var total = qty * unitPrice;

                                    var lines = [
                                        '*Pesan Jeep Merapi*',
                                        'Paket: ' + tourName,
                                        'Nama: ' + name,
                                        'Email: ' + email,
                                        'Telepon: ' + (phone || '-'),
                                        'Tanggal Order: ' + (orderDate || '-'),
                                        'Tanggal Trip: ' + (tripDate || '-'),
                                        'Jumlah Tiket: ' + qty,
                                        'Harga Satuan: ' + formatIDR(unitPrice),
                                        'Total: ' + formatIDR(total)
                                    ];
                                    if (note) lines.push('Catatan: ' + note);

                                    var text = encodeURIComponent(lines.join('\n'));
                                    var phoneWA = "{{ $profil->no_wa ?? '' }}".replace(/[^0-9]/g, '');
                                    var url = 'https://wa.me/' + phoneWA + '?text=' + text;

                                    window.open(url, '_blank');
                                });
                            });
                        </script>
                    </div>
                    <div class="wow fadeInUp" data-wow-duration="1500ms"
                        style="margin-top: 10px; border-radius:10px  box-shadow:none;">
                        <img src="{{ $profil->gambar_1 ? asset('upload/profil/' . $profil->gambar_1) : 'https://jalankebromo.com/wp-content/uploads/2023/06/7.png' }}"
                            alt="Gambar Promo"
                            style="display:block; width:100%; height:auto; aspect-ratio: 3 / 4; object-fit: cover;">
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
