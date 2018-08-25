export default ($config) => {
  return {
    version: $config.get("env.VERSION"),
  };
};
