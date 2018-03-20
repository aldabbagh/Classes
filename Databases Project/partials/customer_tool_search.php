<?php
$post_url = urldecode(basename($_SERVER['REQUEST_URI']));
$start_date = isset($_POST['start-date']) ? $_POST['start-date'] : '';
$end_date = isset($_POST['end-date']) ? $_POST['end-date'] : '';
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : 'all';
$powersource = isset($_POST['powersource']) ? $_POST['powersource'] : '';
$subtype = isset($_POST['subtype']) ? $_POST['subtype'] : '';
?>

<form id="t4r-customer-tool-search"
  action="<?= $post_url; ?>"
  method="post"
  enctype="multipart/form-data"
  autocomplete="off">

  <!-- Start/End dates and keywords -->
  <fieldset class="form-group mb-0">
    <div class="form-row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="start-date">Start date</label>
          <input class="form-control
            <?= isset($errors['start-date']) ? 'is-invalid':''; ?>"
            id="start-date"
            name="start-date"
            type="text"
            placeholder="2017-05-22"
            value="<?= $start_date; ?>">
          <?php if (isset($errors['start-date'])) { ?>
            <div class="invalid-feedback">
              <?= $errors['start-date']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label for="end-date">End date</label>
          <input class="form-control
            <?= isset($errors['end-date']) ? 'is-invalid':''; ?>"
            id="end-date"
            name="end-date"
            type="text"
            placeholder="2017-05-22"
            value="<?= $end_date; ?>">
          <?php if (isset($errors['end-date'])) { ?>
            <div class="invalid-feedback">
              <?= $errors['end-date']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="keyword">Custom search</label>
          <input class="form-control" id="keyword"
            name="keyword"
            type="text"
            placeholder="Keywords"
            value="<?= $keyword; ?>">
        </div>
      </div>
      <!-- Search -->
      <div class="col-md-2">
        <div class="form-group">
          <label class="invisible d-none d-md-block">Search</label>
          <button type="submit" class="btn btn-primary btn-block">
            Search
          </button>
        </div>
      </div>
    </div>
  </fieldset>

  <div class="form-row align-items-center">
    <div class="col-lg-8 col-xl-6">
      <!-- Tool type -->
      <fieldset class="form-group" id="type">
        <legend class="h6">Type</legend>

        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-all"
              type="radio"
              name="type"
              value="all"
              <?= ($type === 'all' || $type === '') ? 'checked' : ''; ?>
              data-subtypes="screwdriver,socket,ratchet,wrench,plier,gun,hammer"
              data-powersources="manual,electric,cordless,gas">
              All tools
          </label>
        </div>

        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-hand"
              type="radio"
              name="type"
              value="hand"
              <?= ($type ==='hand') ? 'checked' : ''; ?>
              data-subtypes="screwdriver,socket,ratchet,wrench,plier,gun,hammer"
              data-powersources="manual">
              Hand tool
          </label>
        </div>

        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-garden"
              type="radio"
              name="type"
              value="garden"
              <?= ($type === 'garden') ? 'checked' : ''; ?>
              data-subtypes="digging,pruning,rake,wheelbarrow,striking"
              data-powersources="manual">
              Garden tool
          </label>
        </div>

        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-ladder"
              type="radio"
              name="type"
              value="ladder"
              <?= ($type === 'ladder') ? 'checked' : ''; ?>
              data-subtypes="straightladder,stepladder"
              data-powersources="manual">
              Ladder
          </label>
        </div>

        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-power"
              type="radio"
              name="type"
              value="power"
              <?= ($type === 'power') ? 'checked' : ''; ?>
              data-subtypes="powerdrill,powersaw,powersander,poweraircompressor,powermixer,powergenerator"
              data-powersources="electric,cordless,gas">
              Power tool
          </label>
        </div>

      </fieldset>
    </div>

    <!-- Powersource and sub-type -->
    <div class="col-lg-2 col-xl-3">
      <div class="form-group">
        <label for="tool-powersource">Power source</label>

        <select class="form-control" id="powersource"
          name="powersource"
          <?php if ($type === '') { ?>
            <?= '' ?>
          <?php } elseif ($type !== 'power' && $type !== 'all') { ?>
            <?= 'readonly' ?>
          <?php } ?>>

          <option value="" disabled hidden
            <?= ($powersource === '') ? 'selected' : '' ?>>
            Select a power source
          </option>

          <option value="manual"
            <?= ($type!=='manual' && $type!=='all') ? 'disabled hidden' : ''; ?>
            <?= ($powersource === 'manual') ? 'selected' : ''; ?>
            data-subtypes="screwdriver,socket,ratchet,wrench,plier,gun,hammer,digging,pruning,rake,wheelbarrow,striking,straightladder,stepladder">
            Manual
          </option>

          <option value="electric"
            <?= ($type!=='power' && $type!=='all') ? 'disabled hidden' : ''; ?>
            <?= ($powersource === 'electric') ? 'selected' : ''; ?>
            data-subtypes="powerdrill,powersaw,powersander,poweraircompressor,powermixer">
            Electric (A/C)
          </option>

          <option value="cordless"
            <?= ($type!=='power' && $type!=='all') ? 'disabled hidden' : ''; ?>
            <?= ($powersource === 'cordless') ? 'selected' : ''; ?>
            data-subtypes="powerdrill,powersaw,powersander">
            Cordless (D/C)
          </option>

          <option value="gas"
            <?= ($type!=='power' && $type!=='all') ? 'disabled hidden' : ''; ?>
            <?= ($powersource === 'gas') ? 'selected' : ''; ?>
            data-subtypes="poweraircompressor,powermixer,powergenerator">
            Gas
          </option>

        </select>
      </div>
    </div>


    <div class="col-lg-2 col-xl-3">
      <div class="form-group">
        <label for="tool-subtype">Sub-type</label>
        <select class="form-control" id="subtype"
          name="subtype">

          <option value="" disabled hidden
            <?= ($subtype === '') ? 'selected' : '' ?>>
            Select a sub-type
          </option>

          <option value="screwdriver"
            <?= ($subtype === 'screwdriver') ? 'selected' : ''; ?>>
            Screwdriver
          </option>
          <option value="socket"
            <?= ($subtype === 'socket') ? 'selected' : ''; ?>>
            Socket
          </option>
          <option value="ratchet"
            <?= ($subtype === 'ratchet') ? 'selected' : ''; ?>>
            Ratchet
          </option>
          <option value="wrench"
            <?= ($subtype === 'wrench') ? 'selected' : ''; ?>>
            Wrench
          </option>
          <option value="plier"
            <?= ($subtype === 'plier') ? 'selected' : ''; ?>>
            Pliers
          </option>
          <option value="gun"
            <?= ($subtype === 'gun') ? 'selected' : ''; ?>>
            Gun
          </option>
          <option value="hammer"
            <?= ($subtype === 'hammer') ? 'selected' : ''; ?>>
            Hammer
          </option>
          <option value="digging"
            <?= ($subtype === 'digging') ? 'selected' : ''; ?>>
            Digger
          </option>
          <option value="pruning"
            <?= ($subtype === 'pruning') ? 'selected' : ''; ?>>
            Pruner
          </option>
          <option value="rake"
            <?= ($subtype === 'rake') ? 'selected' : ''; ?>>
            Rakes
          </option>
          <option value="wheelbarrow"
            <?= ($subtype === 'wheelbarrow') ? 'selected' : ''; ?>>
            Wheelbarrows
          </option>
          <option value="striking"
            <?= ($subtype === 'striking') ? 'selected' : ''; ?>>
            Striking
          </option>
          <option value="straightladder"
            <?= ($subtype === 'straightladder') ? 'selected' : ''; ?>>
            Straight
          </option>
          <option value="stepladder"
            <?= ($subtype === 'stepladder') ? 'selected' : ''; ?>>
            Step
          </option>
          <option value="powerdrill"
            <?= ($subtype === 'powerdrill') ? 'selected' : ''; ?>
            data-powersource="electric,cordless">
            Drill
          </option>
          <option value="powersaw"
            <?= ($subtype === 'powersaw') ? 'selected' : ''; ?>
            data-powersource="electric,cordless">
            Saw
          </option>
          <option value="powersander"
            <?= ($subtype === 'powersander') ? 'selected' : ''; ?>
            data-powersource="electric,cordless">
            Sander
          </option>
          <option value="poweraircompressor"
            <?= ($subtype === 'poweraircompressor') ? 'selected' : ''; ?>
            data-powersource="electric,gas">
            Air-compressor
          </option>
          <option value="powermixer"
            <?= ($subtype === 'powermixer') ? 'selected' : ''; ?>
            data-powersource="electric,gas">
            Mixer
          </option>
          <option value="powergenerator"
            <?= ($subtype === 'powergenerator') ? 'selected' : ''; ?>>
            Generator
          </option>

        </select>
      </div>
    </div>
  </div>

</form>


<?php
unset($start_date);
unset($end_date);
unset($keyword);
unset($type);
unset($powersource);
unset($subtype);
?>
