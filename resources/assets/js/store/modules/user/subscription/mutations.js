export const set = (state, { response }) => {
  state.subscription = response;
};

export const setInvoices = (state, { response }) => {
  state.invoices = response;
};