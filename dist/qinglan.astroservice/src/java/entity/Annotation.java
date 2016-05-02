/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import java.util.Date;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonBackReference;
import org.codehaus.jackson.annotate.JsonIgnore;
import org.codehaus.jackson.annotate.JsonManagedReference;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "annotation")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "Annotation.findAll", query = "SELECT a FROM Annotation a"),
    @NamedQuery(name = "Annotation.findByAnnoId", query = "SELECT a FROM Annotation a WHERE a.annoId = :annoId"),
    @NamedQuery(name = "Annotation.findByTargetType", query = "SELECT a FROM Annotation a WHERE a.targetType = :targetType"),
    @NamedQuery(name = "Annotation.findByTsCreated", query = "SELECT a FROM Annotation a WHERE a.tsCreated = :tsCreated"),
    @NamedQuery(name = "Annotation.findByTsDeleted", query = "SELECT a FROM Annotation a WHERE a.tsDeleted = :tsDeleted")})

    @NamedNativeQuery(name = "nativeSQL.findRecent", query = "SELECT * FROM `annotation` a WHERE a.`ts_created` > DATE_SUB( NOW( ) , INTERVAL ?1 DAY )", resultClass = Annotation.class)

public class Annotation implements Serializable {
    
    @JoinTable(name = "anno_to_obj", joinColumns = {
        @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")}, inverseJoinColumns = {
        @JoinColumn(name = "obj_tar_id", referencedColumnName = "object_id")})
    @ManyToMany(cascade = CascadeType.PERSIST)
    private Collection<ObjectInfo> objectInfoCollection;
    
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "anno_id")
    private Long annoId;
    @Basic(optional = false)
    @NotNull
    @Lob
    @Size(min = 1, max = 65535)
    @Column(name = "anno_value")
    private String annoValue;
    
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
    private Collection<AnnoForUser> annoForUserCollection;
    @OneToOne(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
    private AnnoToAnno annoToAnno;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "annoTarId")
    private Collection<AnnoToAnno> annoToAnnoCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
    private Collection<AnnoForGroup> annoForGroupCollection;
    @OneToOne(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
    private AnnoToSet annoToSet;
//    @OneToMany(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
//    private Collection<AnnoToObj> annoToObjCollection;
    @JoinColumn(name = "user_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userId;
    @JoinColumn(name = "anno_type_id", referencedColumnName = "anno_type_id")
    @ManyToOne(optional = false)
    private AnnoType annoTypeId;
    @OneToOne(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
    private AnnoToAreaPoint annoToAreaPoint;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "annoTarId")
    private Collection<TagToAnno> tagToAnnoCollection;
    @OneToOne(cascade = CascadeType.ALL, mappedBy = "annoSrcId")
    private AnnoToView annoToView;
    
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 10)
    @Column(name = "target_type")
    private String targetType;
    @Basic(optional = false)
    //@NotNull
    @Column(name = "ts_created")
    @Temporal(TemporalType.TIMESTAMP)
    private Date tsCreated;
    @Column(name = "ts_deleted")
    @Temporal(TemporalType.TIMESTAMP)
    private Date tsDeleted;
    

    public Annotation() {
    }

    public Annotation(Long annoId) {
        this.annoId = annoId;
    }

    public Annotation(Long annoId, String annoValue, String targetType, Date tsCreated) {
        this.annoId = annoId;
        this.annoValue = annoValue;
        this.targetType = targetType;
    }

    public Long getAnnoId() {
        return annoId;
    }

    public void setAnnoId(Long annoId) {
        this.annoId = annoId;
    }

    public String getAnnoValue() {
        return annoValue;
    }

    public void setAnnoValue(String annoValue) {
        this.annoValue = annoValue;
    }

    public String getTargetType() {
        return targetType;
    }

    public void setTargetType(String targetType) {
        this.targetType = targetType;
    }

    @XmlTransient     @JsonIgnore
    public Collection<AnnoForUser> getAnnoForUserCollection() {
        return annoForUserCollection;
    }

    public void setAnnoForUserCollection(Collection<AnnoForUser> annoForUserCollection) {
        this.annoForUserCollection = annoForUserCollection;
    }

    public AnnoToAnno getAnnoToAnno() {
        return annoToAnno;
    }

    public void setAnnoToAnno(AnnoToAnno annoToAnno) {
        this.annoToAnno = annoToAnno;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<AnnoToAnno> getAnnoToAnnoCollection() {
        return annoToAnnoCollection;
    }

    public void setAnnoToAnnoCollection(Collection<AnnoToAnno> annoToAnnoCollection) {
        this.annoToAnnoCollection = annoToAnnoCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<AnnoForGroup> getAnnoForGroupCollection() {
        return annoForGroupCollection;
    }

    public void setAnnoForGroupCollection(Collection<AnnoForGroup> annoForGroupCollection) {
        this.annoForGroupCollection = annoForGroupCollection;
    }

    public AnnoToSet getAnnoToSet() {
        return annoToSet;
    }

    public void setAnnoToSet(AnnoToSet annoToSet) {
        this.annoToSet = annoToSet;
    }

    public User getUserId() {
        return userId;
    }

    public void setUserId(User userId) {
        this.userId = userId;
    }

    public AnnoType getAnnoTypeId() {
        return annoTypeId;
    }

    public void setAnnoTypeId(AnnoType annoTypeId) {
        this.annoTypeId = annoTypeId;
    }

    public AnnoToAreaPoint getAnnoToAreaPoint() {
        return annoToAreaPoint;
    }

    public void setAnnoToAreaPoint(AnnoToAreaPoint annoToAreaPoint) {
        this.annoToAreaPoint = annoToAreaPoint;
    }

    @XmlTransient     @JsonIgnore
    public Collection<TagToAnno> getTagToAnnoCollection() {
        return tagToAnnoCollection;
    }

    public void setTagToAnnoCollection(Collection<TagToAnno> tagToAnnoCollection) {
        this.tagToAnnoCollection = tagToAnnoCollection;
    }

    public AnnoToView getAnnoToView() {
        return annoToView;
    }

    public void setAnnoToView(AnnoToView annoToView) {
        this.annoToView = annoToView;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoId != null ? annoId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof Annotation)) {
            return false;
        }
        Annotation other = (Annotation) object;
        if ((this.annoId == null && other.annoId != null) || (this.annoId != null && !this.annoId.equals(other.annoId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.Annotation[ annoId=" + annoId + " ]";
    }

    //@XmlTransient
    //@JsonIgnore
    //@JsonManagedReference("annotation-object")
    public Collection<ObjectInfo> getObjectInfoCollection() {
        return objectInfoCollection;
    }

    public void setObjectInfoCollection(Collection<ObjectInfo> objectInfoCollection) {
        this.objectInfoCollection = objectInfoCollection;
    }

    public Date getTsCreated() {
        return tsCreated;
    }

    public void setTsCreated(Date tsCreated) {
        this.tsCreated = tsCreated;
    }

    public Date getTsDeleted() {
        return tsDeleted;
    }

    public void setTsDeleted(Date tsDeleted) {
        this.tsDeleted = tsDeleted;
    }
    
}
