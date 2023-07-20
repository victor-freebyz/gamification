  <!-- Onboarding Modal -->
  <div class="modal fade" id="modal-onboarding" tabindex="-1" role="dialog" aria-labelledby="modal-onboarding" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content rounded overflow-hidden bg-image bg-image-bottom border-0" style="background-image: url({{ asset('src/assets/media/photos/photo23.jpg') }});">
        <div class="row">
          <div class="col-md-5">
            <div class="p-3 text-end text-md-start">
              {{-- <a class="fw-semibold text-white" href="#" data-bs-dismiss="modal" aria-label="Close">
                Skip Intro
              </a> --}}
            </div>
          </div>
          <div class="col-md-7">
            <div class="bg-body-extra-light shadow-lg">
              <div class="js-slider slick-dotted-inner" data-dots="true" data-arrows="false" data-infinite="false">
                <div class="p-5">
                  <i class="fa fa-award fa-3x text-muted my-4"></i>
                  <h3 class="fs-2 fw-light mb-2">Welcome to Freebyz!</h3>
                  <p class="text-muted">
                    Your global remote jobs and affiliate marketing platform to earn online daily.
                  </p>
                  <button type="button" class="btn btn-alt-primary mb-4" onclick="jQuery('.js-slider').slick('slickGoTo', 1);">
                    Continue <i class="fa fa-arrow-right ms-1"></i>
                  </button>
                </div>
                <div class="slick-slide p-5">
                  <h3 class="fs-2 fw-light mb-2">How to Earn</h3>
                  <p class="text-muted">
                    Complete tasks and earn money. 
                    Your fastest way to earn is to refer your friends. Copy your referral code below to invite your friends and earn ($0.5) N500 on each friend you refer.
                  </p>
                  <center>
                    <div class="col-md-12 mb-2">
                      <div class="input-group">
                        <input type="text" value="{{url('register/'.auth()->user()->referral_code)}}" class="form-control form-control-alt" id="myInput">
                        <button type="button" class="btn btn-alt-secondary" onclick="myFunction()" onmouseout="outFunc()">
                          <i class="fa fa-copy"></i>
                        </button>
                      </div>
                    </div>
                  </center>
                  <h3 class="fs-2 fw-light mb-2">Hire Online Workers</h3>
                  <p class="text-muted">
                    Promote your affiliate links, grow your Youtube, Tiktok and other social media pages to earn more money online. 
                  </p>
                  <a href="{{ url('complete/welcome') }}" class="btn btn-alt-primary mb-4">
                    Start Earning Now! <i class="fa fa-arrow-right ms-1"></i>
                  </a>
                  {{-- <button type="button" class="btn btn-alt-primary mb-4" onclick="jQuery('.js-slider').slick('slickGoTo', 2);">
                   
                  </button> --}}
                </div>
                {{-- <div class="slick-slide p-5">
                  <i class="fa fa-user-check fa-3x text-muted my-4"></i>
                  <h3 class="fs-2 fw-light">Let us know your name</h3>
                  <form class="mb-3">
                    <div class="mb-4">
                      <input type="text" class="form-control form-control-alt" id="onboard-first-name" name="onboard-first-name" placeholder="Enter your first name..">
                    </div>
                    <div class="mb-4">
                      <input type="text" class="form-control form-control-alt" id="onboard-last-name" name="onboard-last-name" placeholder="Enter your last name..">
                    </div>
                  </form>
                  <button type="button" class="btn btn-primary mb-4" data-bs-dismiss="modal" aria-label="Close">
                    Get Started <i class="fa fa-check opacity-50 ms-1"></i>
                  </button>
                </div> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Onboarding Modal -->

  <script>
    function myFunction() {
      var copyText = document.getElementById("myInput");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(copyText.value);
      
      var tooltip = document.getElementById("myTooltip");
      tooltip.innerHTML = "Copied: " + copyText.value;
    }
    
    function outFunc() {
      var tooltip = document.getElementById("myTooltip");
      tooltip.innerHTML = "Copy to clipboard";
    }
    </script>