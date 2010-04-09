class AddRandomKeyToSuites < ActiveRecord::Migration
  def self.up
    add_column(:suites, :key, :string, {:limit => 10})
  end

  def self.down
    remove_column(:suites, :key)
  end
end
