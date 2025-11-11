<style>
@font-face {
  font-family: 'EuropaLight';
  src: url('{{storage_path('fonts/')}}EuropaLight.ttf') format("truetype");
  font-weight: 300;
  font-style: normal; 
}

@font-face {
  font-family: 'EuropaRegular';
  src: url('{{storage_path('fonts/')}}EuropaRegular.ttf') format("truetype");
  font-weight: 400;
  font-style: normal; 
}

@font-face {
  font-family: 'EuropaBold';
  src: url('{{storage_path('fonts/')}}EuropaBold.ttf') format("truetype");
  font-weight: 700;
  font-style: normal; 
}

body {
  color: #000000;
  font-size: 10pt;
  font-family: 'EuropaRegular', Arial, sans-serif;
  font-weight: 400;
  line-height: 1;
}

strong {
  font-family: 'EuropaBold', Arial, sans-serif;
  font-weight: 700;
}

table {
  border-collapse: collapse;
  border-spacing: 0;
}

td {
  font-family: 'EuropaRegular', Helvetica, Arial, sans-serif;
  padding: 0;
  vertical-align: top;
}

th {
  font-family: 'EuropaRegular', Helvetica, Arial, sans-serif;
  font-weight: 400;
  text-align: left;
}

img {
  border: 0;
  vertical-align: middle;
}

table {
  width: 100%;
}

table td {
  text-align: left;
  vertical-align: top;
}

h1, h2, h3 {
  font-family: 'EuropaBold', Helvetica, Arial, sans-serif;
  font-weight: 700;
}

p {
  margin-bottom: 5mm;
}

ul, li {
  margin: 0;
  padding: 0;
}

li {
  margin-left: 4mm;
}

/* Page */
.page {
  margin-bottom: 0;
}

.page-header {
  display: inline-block;
  height: 30mm;
  left: 0;
  position: fixed;
  top: -50mm;
  width: 210mm;
  z-index: 100;
}

.page-header img {
  display: block;
  height: auto;
  left: 6.5mm;
  position: fixed;
  top: -50mm;
  width: 30.96mm;
}

.page-header h1 {
  font-family: 'EuropaLight', Arial, sans-serif;
  font-weight: 300;
  font-size: 20pt;
  margin: 0;
  padding: 0;
  position: absolute;
  line-height: .7;
  left: 134.5mm;
  top: 0mm;
}

.page-footer {
  bottom: -15mm;
  position: fixed;
  left: 20mm;
  z-index: 100;
}

.page-address,
.page-content {
  width: 140mm;
}

.page-address {
  top: -10mm;
  left: 47.5mm;
  position: absolute;
}

.page-content {
  position: relative;
  left: 47.5mm;
  margin-top: 30mm;
}

.page-content-header {
  margin-bottom: 12.5mm;
}

.order-details {
  border-top: .05mm solid #000000;
  margin-bottom: 7.5mm;
  page-break-inside: avoid;
  width: 140mm;
}

.order-detail-item {
  border-bottom: .05mm solid #000000;
  height: 7.5mm;
  vertical-align: middle;
  width: 113mm;
}

.order-detail-item.order-detail-item--currency {
  width: 5mm !important;
} 

.order-detail-item.order-detail-item--price {
  width: 17mm !important;
}

.order-detail-item.order-detail-item--address {
  width: 140mm !important;
}

.break {
  page-break-after: always;
}

/* Helpers */
.align-right {
  text-align: right;
}

.align-left {
  text-align: left;
}

.valign-top {
  vertical-align: top;
}

.valign-middle {
  vertical-align: middle;
}

.valign-bottom {
  vertical-align: bottom;
}

.clearfix:after {
  visibility: hidden;
  display: block;
  font-size: 0;
  content: " ";
  clear: both;
  height: 0;
}

.font-bold {
  font-family: 'EuropaBold', Arial, sans-serif;
  font-weight: 700;
}

.font-regular {
  font-family: 'EuropaRegular', Arial, sans-serif;
  font-weight: 400;
}

.font-light {
  font-family: 'EuropaLight', Arial, sans-serif;
  font-weight: 300;
}

.mb-xl {
  margin-top: 160mm;
}
</style>