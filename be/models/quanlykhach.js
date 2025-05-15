'use strict';
const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class QuanLyKhach extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // define association here
    }
  }
  QuanLyKhach.init({
    HoTen: DataTypes.STRING,
    SDT: DataTypes.INTEGER,
    Email: DataTypes.STRING,
    CCCD: DataTypes.INTEGER,
    GioiTinh: DataTypes.STRING,
    NgaySinh: DataTypes.DATE,
    DiaChiThuongTru: DataTypes.STRING
  }, {
    sequelize,
    modelName: 'QuanLyKhach',
  });
  return QuanLyKhach;
};