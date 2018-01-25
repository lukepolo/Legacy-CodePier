import NProgress from "nprogress";
window.NProgress = NProgress;

export default function loadProgressBar(config) {
  let requestsCounter = 0;
  let spinnerTimeout = null;

  const setupStartProgress = () => {
    axios.interceptors.request.use((config) => {
      if (!spinnerTimeout) {
        spinnerTimeout = setTimeout(() => {
          turnOnSpinner();
        }, 1500);
      }

      requestsCounter++;
      NProgress.start();
      return config;
    });
  };

  const setupStopProgress = () => {
    const responseFunc = (response) => {
      if (--requestsCounter === 0) {
        turnOffSpinner();
        NProgress.done();
      }
      return response;
    };

    const errorFunc = (error) => {
      if (--requestsCounter === 0) {
        turnOffSpinner();
        NProgress.done();
      }
      return Promise.reject(error);
    };

    axios.interceptors.response.use(responseFunc, errorFunc);
  };

  const getNprogressElement = () => {
    return document.getElementById("nprogress");
  };

  const turnOnSpinner = () => {
    if (getNprogressElement()) {
      getNprogressElement().classList.add("show-spinner");
    }
  };

  const turnOffSpinner = () => {
    clearTimeout(spinnerTimeout);
    spinnerTimeout = null;
    if (getNprogressElement()) {
      getNprogressElement().classList.remove("show-spinner");
    }
  };

  NProgress.configure(config);
  setupStartProgress();
  setupStopProgress();
}
