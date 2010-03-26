class Weakness < ActiveRecord::Base
  has_many :child_relationships,  :foreign_key => 'parent_id',
                                  :class_name => 'WeaknessRelationship',
                                  :dependent => :destroy
  has_many :children,             :through => :child_relationships
  
  has_many :parent_relationships, :foreign_key => 'child_id',
                                  :class_name => 'WeaknessRelationship',
                                  :dependent => :destroy
  has_many :parents,             :through => :parent_relationships
  
  has_many :vulnerabilities
  
  WEAKNESS_IDS = {'View' => 0, 'Category' => 1, 'Weakness' => 2, 'Compound_Element' => 3}
  
  def self.update_count_and_score
    start_time = Time.now
    print "recalculating average weakness scores and counts..."
    Weakness.all.each do |weakness|
      weakness.score = 0.0;
      weakness.count = 0
      weakness.vulnerabilities.each do |vulnerability|
        weakness.count += 1;
        weakness.score += vulnerability.score
      end
  
      if weakness.count > 0
        weakness.score /= weakness.count
        weakness.save
      end
    end
    puts "done, #{Time.now - start_time} seconds elapsed."
  end
  
end
