'use strict';
/** @type {import('sequelize-cli').Migration} */
module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.createTable('QuanLyKhaches', {
      id: {
        allowNull: false,
        autoIncrement: true,
        primaryKey: true,
        type: Sequelize.INTEGER
      },
      HoTen: {
        type: Sequelize.STRING
      },
      SDT: {
        type: Sequelize.INTEGER
      },
      Email: {
        type: Sequelize.STRING
      },
      CCCD: {
        type: Sequelize.INTEGER
      },
      GioiTinh: {
        type: Sequelize.STRING
      },
      NgaySinh: {
        type: Sequelize.DATE
      },
      DiaChiThuongTru: {
        type: Sequelize.STRING
      },
      createdAt: {
        allowNull: false,
        type: Sequelize.DATE
      },
      updatedAt: {
        allowNull: false,
        type: Sequelize.DATE
      }
    });
  },
  async down(queryInterface, Sequelize) {
    await queryInterface.dropTable('QuanLyKhaches');
  }
};