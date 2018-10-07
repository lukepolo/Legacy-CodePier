import { SslCertificatesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SslCertificatesState) => {
      return state;
    },
  };
}
