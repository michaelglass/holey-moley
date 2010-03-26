class Weakness < ActiveRecord::Base
  has_many :child_relationships,  :foreign_key => 'parent_id',
                                  :class_name => 'WeaknessRelationship',
                                  :dependent => :destroy
  has_many :children,             :through => :weakness_relationships
  
  has_many :parent_relationships, :foreign_key => 'child_id',
                                  :class_name => 'WeaknessRelationship',
                                  :dependent => :destroy
    
  has_many :children,             :through => :parent_relationships
  
  WEAKNESS_IDS = {'View' => 0, 'Category' => 1, 'Weakness' => 2, 'Compound_Element' => 3}
end
