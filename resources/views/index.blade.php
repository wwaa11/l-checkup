@extends('layouts.app')
@section('content')
    <div class="flex">
        <div class="">
            <div class="mb-3">
                {{ $text->name }} : {{ $hnDetail->name }} ( {{ $hnDetail->HN }} )
            </div>
            <div class="mb-3">
                {{ $text->app_no }} : {{ $hnDetail->appNo }}
            </div>
            <div class="mb-3">
                {{ $text->app_date }} : {{ $hnDetail->appDate }}
            </div>
            <div class="mb-3">
                {{ $text->app_time }} : {{ $hnDetail->appTime }}
            </div>
            <div id="checkLo" class="m-auto">
                <div class="text-center rounded p-3 btn-secondary">
                    {{ $text->range_check }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const sleep = (delay) => new Promise((resolve) => setTimeout(resolve, delay))
        Array.prototype.random = function() {
            return this[Math.floor((Math.random() * this.length))];
        }
        var lat = '-';
        var log = '-';

        $(document).ready(function() {
            navigator.geolocation.getCurrentPosition(success, error, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0,
            })

            setTimeout(function() {
                checkLocation()
            }, 1 * 500);
        });

        function success(pos) {
            const crd = pos.coords;
            lat = crd.latitude;
            log = crd.longitude;
        }

        function error(err) {
            Swal.fire({
                title: "Please allow location access.",
                text: "โปรดอนุญาตการเข้าถึงตำแหน่ง และ ปิดเปิดใหม่อีกครั้ง",
                icon: "error",
                allowOutsideClick: false,
                showConfirmButton: false,
                showCancelButton: false,
            });
        }

        async function checkLocation() {
            if (lat == '-' || log == '-') {
                setTimeout(function() {
                    checkLocation()
                }, 1 * 1000);
            } else {
                const formData = new FormData();
                formData.append('hn', {{ $hnDetail->HN }});
                formData.append('lat', lat);
                formData.append('log', log);
                const res = await axios.post("{{ env('APP_URL') }}" + "/checkLocation", formData, {
                    "Content-Type": "multipart/form-data"
                }).then((res) => {
                    $('#checkLo').html(res.data.html)
                })
            }
        }




        async function selectItem(hn) {
            wait = [500, 800, 1000, 1200, 1500].random()
            await sleep(wait)

            if ('{{ session('langSelect') }}' == "TH") {
                text = "กรุณารอสักครู่"
                err = "กรุณาลองอีกครั้ง"
            } else {
                text = "Please, wait."
                err = "Err.Try again."
            }

            $('#sleItem').hide();
            Swal.fire({
                title: text,
                icon: "warning",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });
            const formData = new FormData();
            formData.append('hn', hn);
            const res = await axios.post("{{ env('APP_URL') }}" + "getqueu", formData, {
                "Content-Type": "multipart/form-data"
            }).then((res) => {
                console.log(res)
                if (res.status == 200) {
                    Swal.fire({
                        title: 'Success.',
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    window.location.href = '{{ env('APP_URL') }}viewqueue/' + hn
                } else {
                    Swal.fire({
                        title: 'Error.',
                        icon: "error",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                }
            }).catch(function(error) {
                Swal.fire({
                    title: err,
                    icon: "error",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });
            });
        }
    </script>
@endsection
