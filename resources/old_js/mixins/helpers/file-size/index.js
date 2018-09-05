export const megaBytesToHumanReadable = (megabytes, decimals = 2) => {
  if (megabytes === 0) return "0 MB";
  let k = 1024,
    dm = decimals || 2,
    sizes = ["MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
    i = Math.floor(Math.log(megabytes) / Math.log(k));
  return parseFloat((megabytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
};
