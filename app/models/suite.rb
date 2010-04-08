class Suite < ActiveRecord::Base
  has_many( :tests, {:dependent => :destroy} )
  validates_presence_of :top, :start, :end, :weight
  validates_numericality_of :top, :less_than => 20, 
                                  :greater_than => 0
  validates_numericality_of :weight, :less_than_or_equal_to => 1,
                                     :greater_than_or_equal_to => 0
  validates_each :start, :end do |record, attr, value|
    if value < Date.parse("1996-01-01") || value > Date.today
      record.errors.add attr, 'must be between 1996 and today.'
    end
  end
  def validate
    errors.add_to_base "End must be after Start" if self.end <= self.start
  end
end
