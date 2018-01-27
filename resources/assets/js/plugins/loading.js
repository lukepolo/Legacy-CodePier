import NProgress from "nprogress";
window.NProgress = NProgress;

export default function loadProgressBar(config) {
  let timeoutSet = false;
  let requestsCounter = 0;
  let spinnerTimeout = null;

  const setupStartProgress = () => {
    axios.interceptors.request.use(config => {
      if (timeoutSet === false) {
        spinnerTimeout = setTimeout(() => {
          turnOnSpinner();
        }, 1500);
        timeoutSet = true;
      }

      requestsCounter++;
      NProgress.start();

      return config;
    });
  };

  const setupStopProgress = () => {
    const responseFunc = response => {

      if (--requestsCounter === 0) {
        turnOffSpinner();
        NProgress.done();
      }
      return response;
    };

    const errorFunc = error => {
      if (--requestsCounter === 0) {
        turnOffSpinner();
        NProgress.done();
      }
      return Promise.reject(error);
    };

    axios.interceptors.response.use(responseFunc, errorFunc);
  };

  const turnOnSpinner = () => {
    let spinnerElement = getSpinnerElement();

    if (!spinnerElement) {
      document.body.innerHTML +=
        '<div id="spinner" class="sk-container">' +
        '<div class="sk-folding-cube">' +
        '  <div class="sk-cube1 sk-cube"></div>' +
        '  <div class="sk-cube2 sk-cube"></div>' +
        '  <div class="sk-cube4 sk-cube"></div>' +
        '  <div class="sk-cube3 sk-cube"></div>' +
        "</div>" +
        "</div>";
    }
  }

  const getSpinnerElement = () => {
    return document.getElementById("spinner");
  }

  const turnOffSpinner = () => {
    clearTimeout(spinnerTimeout);
    spinnerTimeout = null;
    let spinnerElement = getSpinnerElement();
    if (spinnerElement) {
      spinnerElement.remove();
    }
  }

  NProgress.configure(config);
  setupStartProgress();
  setupStopProgress();
}
