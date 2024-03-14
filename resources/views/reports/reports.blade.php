@extends('layout')


@section('content')

<div class="container mt-5">
    <div class="container px-4 py-5" id="icon-grid">
    <h2 class="pb-2 border-bottom">Report grid</h2>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 py-5">
      <div class="col d-flex align-items-start">
          <a href="reports/patients"><i class="bi bi-people-fill me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Patients</h3>
          <p>Patients distribution reports, summary reports, etc.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <a href="reports/medservices"><i class="bi bi-lungs-fill me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Medical Services</h3>
          <p>Medical Services report summaries.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <a href="reports/investigations"><i class="bi bi-eyedropper me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Laboratory</h3>
          <p>Paragraph of text beneath the heading to explain the heading.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <a href="reports/pharmacy"><i class="bi bi-capsule me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Pharmacy</h3>
          <p>Paragraph of text beneath the heading to explain the heading.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <a href="reports/hospitalandothers"><i class="bi bi-hospital me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Hospital/Others</h3>
          <p>Paragraph of text beneath the heading to explain the heading.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <a href="reports/accounts"><i class="bi bi-receipt me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Accounts</h3>
          <p>Paragraph of text beneath the heading to explain the heading.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <a href="reports/resources"><i class="bi bi-minecart-loaded me-3 btn btn-outline-primary"></i></a>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Resources</h3>
          <p>Paragraph of text beneath the heading to explain the heading.</p>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <i class="bi bi-people me-3 btn btn-outline-primary"></i>
        <div>
          <h3 class="fw-bold mb-0 fs-4 text-primary">Staff </h3>
          <p>Paragraph of text beneath the heading to explain the heading.</p>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection