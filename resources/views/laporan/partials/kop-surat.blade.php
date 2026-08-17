{{-- Kop Surat DASEN - Compatible with DomPDF --}}
<style>
    .kop-wrapper {
        width: 100%;
        margin-bottom: 2px;
    }
    .kop-border {
        border: 3px solid #1a1a2e;
        width: 100%;
    }
    .kop-table {
        width: 100%;
        border-collapse: collapse;
    }
    .kop-logo-cell {
        width: 140px;
        background: #1a1a2e;
        text-align: center;
        vertical-align: middle;
        padding: 6px 10px;
    }
    .kop-logo-img {
        height: 50px;
        width: auto;
    }
    .kop-center-cell {
        text-align: center;
        vertical-align: middle;
        padding: 6px 15px;
    }
    .kop-tagline {
        background: #c5a34d;
        color: white;
        font-size: 13px;
        font-style: italic;
        font-family: 'Georgia', 'Times New Roman', serif;
        letter-spacing: 1.5px;
        padding: 5px 25px;
        display: inline-block;
    }
    .kop-cert-cell {
        width: 130px;
        text-align: center;
        vertical-align: bottom;
        padding: 5px 8px 8px 8px;
    }
    .kop-cert-img {
        height: 35px;
        width: auto;
        margin: 0 1px;
    }
    .kop-line {
        height: 3px;
        background: #c5a34d;
        border: none;
        margin: 0;
    }
</style>

<div class="kop-wrapper">
    <div class="kop-border">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    <img class="kop-logo-img" src="{{ public_path('img/logo.png') }}" alt="DASEN">
                </td>
                <td class="kop-center-cell">
                    <span class="kop-tagline">Every Connection Start Here</span>
                </td>
                <td class="kop-cert-cell">
                    <img class="kop-cert-img" src="{{ public_path('img/iso.png') }}" alt="ISO">
                    <img class="kop-cert-img" src="{{ public_path('img/tia.png') }}" alt="TIA">
                </td>
            </tr>
        </table>
    </div>
</div>
